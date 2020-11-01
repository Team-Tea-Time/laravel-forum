<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use TeamTeaTime\Forum\Http\Requests\BaseRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class RestorePosts extends BaseRequest implements FulfillableRequest
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
        $posts = $this->posts()->get();
        foreach ($posts as $post)
        {
            if (! $this->user()->can('restore', $post)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        $posts = $this->posts();
        $posts->restore();
        
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
        $query = $this->user()->can('viewTrashedPosts') ? Post::withTrashed() : Post::query();
        return $query->whereIn('id', $this->validated()['posts']);
    }
}
