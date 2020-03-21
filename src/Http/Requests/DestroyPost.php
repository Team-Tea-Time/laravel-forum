<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Post;

class DestroyPost extends FormRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        $post = $this->route('post');
        return ! $post->isFirst && $this->user()->can('delete', $post);
    }

    public function rules(): array
    {
        return [
            'permadelete' => ['boolean']
        ];
    }

    public function fulfill()
    {
        $post = $this->route('post');

        if (! config('forum.general.soft_deletes') || $this->input('permadelete') && method_exists($post, 'forceDelete'))
        {
            $post->forceDelete();
        }
        else
        {
            $post->delete();
        }

        return $post;
    }
}
