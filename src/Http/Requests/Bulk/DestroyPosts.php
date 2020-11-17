<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Events\UserBulkDeletedPosts;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Http\Requests\Traits\HandlesDeletion;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Post;

class DestroyPosts extends FormRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation, HandlesDeletion;

    public function rules(): array
    {
        return [
            'posts' => ['required', 'array'],
            'permadelete' => ['boolean']
        ];
    }

    public function authorizeValidated(): bool
    {
        $posts = $this->postsAsModels()->get();

        foreach ($posts as $post)
        {
            if (! $this->user()->can('delete', $post)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        $posts = $this->postsAsModels()->get();

        $rowsAffected = $this->isPermaDeleting()
            ? $this->posts()->delete()
            : $this->posts()->whereNull('deleted_at')->update(['deleted_at' => DB::raw('now()')]);

        if ($rowsAffected == 0) return collect();

        event(new UserBulkDeletedPosts($this->user(), $posts));

        $threads = $posts->pluck('thread')->unique();
        $categories = $threads->pluck('category')->unique();

        foreach ($threads as $thread)
        {
            // @TODO: handle deletion of thread if no posts remain

            $removedPostCount = $posts->where('thread_id', $thread->id)->whereNull('deleted_at')->count();

            // Skip updates if the affected posts were already soft-deleted
            if ($removedPostCount == 0) continue;

            $thread->updateWithoutTouch([
                'last_post_id' => $thread->getLastPost()->id,
                'reply_count' => DB::raw("reply_count - {$removedPostCount}")
            ]);

            $thread->posts->each(function ($p)
            {
                $p->updateWithoutTouch(['sequence' => $p->getSequenceNumber()]);
            });
        }

        foreach ($categories as $category)
        {
            $categoryThreadIds = $threads->where('category_id', $category->id)->pluck('id');
            $removedPostCount = $posts->whereIn('thread_id', $categoryThreadIds)->whereNull('deleted_at')->count();

            // Skip updates if the affected posts were already soft-deleted
            if ($removedPostCount == 0) continue;

            $category->updateWithoutTouch([
                'latest_active_thread' => $category->getLatestActiveThreadId(),
                'post_count' => DB::raw("post_count - {$removedPostCount}")
            ]);
        }

        return $posts;
    }

    private function posts(): QueryBuilder
    {
        $query = DB::table(Post::getTableName());

        if (! $this->user()->can('viewTrashedPosts'))
        {
            $query = $query->whereNull('deleted_at');
        }

        return $query->whereIn('id', $this->validated()['posts']);
    }

    private function postsAsModels(): EloquentBuilder
    {
        $query = Post::query();

        if ($this->user()->can('viewTrashedPosts'))
        {
            $query = $query->withTrashed();
        }

        return $query->whereIn('id', $this->validated()['posts']);
    }
}
