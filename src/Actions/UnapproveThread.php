<?php

namespace TeamTeaTime\Forum\Actions;

use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Models\{
    Post,
    Thread
};

class UnapproveThread extends BaseAction
{
    private Thread $thread;

    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }

    protected function transact()
    {
        if (!$this->thread->isApproved) {
            return null;
        }

        $postCount = $this->thread->approvedPostCount;

        Post::withoutTimestamps(fn () => $this->thread->firstPost()->update([
            'approved_at' => null
        ]));

        Thread::withoutTimestamps(fn () => $this->thread->update([
            'approved_at' => null
        ]));

        $category = $this->thread->category;

        $attributes = [
            'thread_count' => DB::raw('thread_count - 1'),
            'post_count' => DB::raw("post_count - {$postCount}"),
        ];

        if ($category->newest_thread_id === $this->thread->id) {
            $attributes['newest_thread_id'] = $category->getNewestThreadId();
        }
        if ($category->latest_active_thread_id === $this->thread->id) {
            $attributes['latest_active_thread_id'] = $category->getLatestActiveThreadId();
        }

        $category->update($attributes);

        return $this->thread;
    }
}
