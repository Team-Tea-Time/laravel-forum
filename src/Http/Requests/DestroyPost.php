<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Post;

class DestroyPost extends FormRequest implements FulfillableRequest
{
    public function authorize(Post $post): bool
    {
        return $this->user()->can('delete', $post);
    }

    public function fulfill()
    {
        $permanent = ! config('forum.general.soft_deletes');

        $post = $this->route('post');
        $permanent && method_exists($post, 'forceDelete') ? $post->forceDelete() : $post->delete();

        return $post;
    }
}
