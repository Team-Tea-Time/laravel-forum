<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Events\UserBulkRestoredPosts;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Post;

class RestorePosts extends FormRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation;

    public function rules(): array
    {
        return [
            'posts' => ['required', 'array']
        ];
    }

    public function authorizeValidated(): bool
    {
        $posts = $this->postsAsModels()->get();
        foreach ($posts as $post)
        {
            if (! $this->user()->can('restore', $post)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        $posts = $this->postsAsModels()->get();

        $this->posts()->update(['deleted_at' => null]);

        $threads = $posts->pluck('thread')->unique();
        $postsByThread = $posts->groupBy('thread_id');
        
        foreach ($threads as $thread)
        {
            $threadPosts = $postsByThread->get($thread->id);
            $thread->updateWithoutTouch([
                'last_post_id' => $thread->getLastPost()->id,
                'reply_count' => DB::raw("reply_count + {$threadPosts->count()}")
            ]);
        }

        $categories = $threads->pluck('category')->unique();
        $threadsByCategory = $threads->groupBy('category_id');

        foreach ($categories as $category)
        {
            $categoryThreads = $threadsByCategory->get($category->id);
            $postCount = $posts->whereIn('thread_id', $categoryThreads->pluck('id'))->count();
            $category->updateWithoutTouch([
                'latest_active_thread_id' => $category->getLatestActiveThreadId(),
                'post_count' => DB::raw("post_count + {$postCount}")
            ]);
        }

        event(new UserBulkRestoredPosts($this->user(), $posts));

        return $posts;
    }

    private function posts(): QueryBuilder
    {
        $query = DB::table(Post::getTableName())->whereNotNull(Post::DELETED_AT);
        return $query->whereIn('id', $this->validated()['posts']);
    }

    private function postsAsModels(): EloquentBuilder
    {
        $query = Post::query()->onlyTrashed();
        return $query->whereIn('id', $this->validated()['posts']);
    }
}
