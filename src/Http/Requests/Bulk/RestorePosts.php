<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Http\Requests\Traits\BulkQueriesPosts;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class RestorePosts extends FormRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation, BulkQueriesPosts;

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
        $posts = $this->posts()->get();
        foreach ($posts as $post)
        {
            $post->restore();
        }

        return $posts;
    }
}
