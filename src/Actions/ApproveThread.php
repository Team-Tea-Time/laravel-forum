<?php

namespace TeamTeaTime\Forum\Actions;

use Carbon\Carbon;
use TeamTeaTime\Forum\Models\Thread;

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

        $this->thread->updateWithoutTouch([
            'approved_at' => Carbon::now(),
        ]);

        $this->thread->firstPost->updateWithoutTouch([
            'approved_at' => Carbon::now(),
        ]);

        $this->thread->category->updateWithoutTouch([
            'newest_thread_id' => max($this->thread->id, $this->thread->category->newest_thread_id),
            'latest_active_thread_id' => $this->thread->category->getLatestActiveThreadId(),
        ]);

        return $this->thread;
    }
}
