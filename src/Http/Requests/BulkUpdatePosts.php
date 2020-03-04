<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Post;

class BulkUpdatePosts extends FormRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        $posts = $this->getPosts();

        foreach ($posts as $post)
        {
            if (! $this->user()->can('edit', $post)) return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['in:restore']
        ];
    }

    public function fulfill()
    {
        $posts = $this->getPosts();
        foreach ($posts as $post)
        {
            $post->restore();
        }

        return $posts;
    }

    private function getPosts()
    {
        return Post::find($this->input('items'));
    }
}
