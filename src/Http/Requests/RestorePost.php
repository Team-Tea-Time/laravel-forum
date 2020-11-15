<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Events\UserRestoredPost;
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

        $post->thread->updateWithoutTouch([
            'last_post_id' => $post->id,
            'reply_count' => DB::raw('reply_count + 1')
        ]);

        $post->thread->category->updateWithoutTouch([
            'latest_active_thread_id' => $post->thread->category->getLatestActiveThreadId(),
            'post_count' => DB::raw('post_count + 1')
        ]);

        event(new UserRestoredPost($this->user(), $post));

        return $post;
    }
}
