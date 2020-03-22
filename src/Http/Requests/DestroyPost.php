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
        return $post->sequence != 1 && $this->user()->can('delete', $post);
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

        if (config('forum.general.soft_deletes') && isset($this->validated()['permadelete']) && $this->validated()['permadelete'] && method_exists($post, 'forceDelete'))
        {
            $post->forceDelete();
        }
        else
        {
            $post->delete();
        }

        $post->thread->syncLastPost();
        $post->thread->category->syncLatestActiveThread();

        return $post;
    }
}
