<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Query\Builder;
use TeamTeaTime\Forum\Events\UserBulkDestroyedPosts;
use TeamTeaTime\Forum\Http\Requests\BaseRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Post;

class DestroyPosts extends BaseRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation;

    public function rules(): array
    {
        return [
            'posts' => ['required', 'array'],
            'permadelete' => ['boolean']
        ];
    }

    public function authorizeValidated(): bool
    {
        $posts = $this->posts()->get();
        foreach ($posts as $post)
        {
            if (! $this->user()->can('delete', $post)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        $posts = $this->posts();

        if (config('forum.general.soft_deletes') && $this->isPermaDeleteRequested() && method_exists(Post::class, 'forceDelete'))
        {
            foreach ($posts as $post)
            {
                $post->forceDelete();
            }
        }
        else
        {
            foreach ($posts as $post)
            {
                $post->delete();
            }
        }

        event(new UserBulkDeletedPosts($this->user(), $posts));
        
        $postsByThread = $posts->select('thread_id')->distinct()->get();
        foreach ($postsByThread as $post)
        {
            $post->thread->syncLastPost();
            $post->thread->category->syncLatestActiveThread();
        }

        return $posts->get();
    }

    private function posts(): Builder
    {
        $query = \DB::table(Post::getTableName());

        if (! $this->user()->can('viewTrashedPosts'))
        {
            $query = $query->whereNull('deleted_at');
        }

        return $query->whereIn('id', $this->validated()['posts']);
    }
}
