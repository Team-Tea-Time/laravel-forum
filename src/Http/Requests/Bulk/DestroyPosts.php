<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Http\Requests\Traits\BulkQueriesPosts;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class DestroyPosts extends FormRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation, BulkQueriesPosts;

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
        $posts = $this->posts()->get();

        if (config('forum.general.soft_deletes') && $this->validated()['permadelete'] && method_exists(Post::class, 'forceDelete'))
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

        return $posts;
    }
}
