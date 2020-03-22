<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class RestorePost extends FormRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        $post = $this->route('post');
        return $this->user()->can('restore', $post);
    }

    public function rules(): array
    {
        return [];
    }

    public function fulfill()
    {
        $post = $this->route('post');
        $post->restoreWithoutTouch();
        $post->thread->update(['last_post_id' => $post->id]);
        $post->thread->category->syncLatestActiveThread();

        return $post;
    }
}
