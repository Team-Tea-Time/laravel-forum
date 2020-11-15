<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Events\UserRestoredThread;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class RestoreThread extends BaseRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        $thread = $this->route('thread');
        return $this->user()->can('restore', $thread);
    }

    public function rules(): array
    {
        return [];
    }

    public function fulfill()
    {
        $thread = $this->route('thread');
        $thread->restoreWithoutTouch();
        $thread->posts()->restore();

        $category = $thread->category;
        $category->update([
            'newest_thread_id' => max($thread->id, $category->newest_thread_id),
            'latest_active_thread_id' => $category->getLatestActiveThreadId(),
            'thread_count' => DB::raw("thread_count + 1"),
            'post_count' => DB::raw("post_count + {$thread->postCount}")
        ]);

        event(new UserRestoredThread($this->user(), $thread));

        return $thread;
    }
}
