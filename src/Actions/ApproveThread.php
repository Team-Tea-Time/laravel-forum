<?php

namespace TeamTeaTime\Forum\Actions;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Models\{
    Post,
    Thread
};

class ApproveThread extends BaseAction
{
    private Thread $thread;

    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }

    protected function transact()
    {
        if ($this->thread->isApproved) {
            return null;
        }

        Post::withoutTimestamps(fn () => $this->thread->firstPost()->update([
            'approved_at' => Carbon::now()
        ]));

        Thread::withoutTimestamps(fn () => $this->thread->update([
            'approved_at' => Carbon::now()
        ]));

        $this->thread->category->update([
            'thread_count' => DB::raw('thread_count + 1'),
            'post_count' => DB::raw("post_count + {$this->thread->approvedPostCount}"),
            'newest_thread_id' => max($this->thread->id, $this->thread->category->newest_thread_id),
            'latest_active_thread_id' => $this->thread->category->getLatestActiveThreadId(),
        ]);

        return $this->thread;
    }
}
