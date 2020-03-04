<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Post;

class BulkDestroyPosts extends FormRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        $posts = $this->getPosts();

        foreach ($posts as $post)
        {
            if (! $this->user()->can('delete', $post)) return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['in:delete,permadelete']
        ];
    }

    public function fulfill()
    {
        $permanent = ! config('forum.general.soft_deletes');

        $posts = $this->getPosts();

        if ($permanent && method_exists(Post::class, 'forceDelete'))
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

    private function getPosts()
    {
        return Post::find($this->input('items'));
    }
}
