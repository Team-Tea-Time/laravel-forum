<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\Events\UserRestoredPost;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class RestorePost extends BaseRequest implements FulfillableRequest
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

        event(new UserRestoredPost($this->user(), $post));

        return $post;
    }
}
