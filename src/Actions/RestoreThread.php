<?php

namespace TeamTeaTime\Forum\Actions;

use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Models\Thread;

class RestoreThread extends BaseAction
{
    private Thread $thread;

    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }

    protected function transact()
    {
        if (!$this->thread->trashed()) {
            return null;
        }

        Thread::withoutTimestamps(fn () => $this->thread->setTouchedRelations([])->restore());

        // Skip category update if the thread isn't approved
        if (!$this->thread->isApproved) {
            return $this->thread;
        }

        $category = $this->thread->category;
        $category->update([
            'thread_count' => DB::raw("thread_count + 1"),
            'post_count' => DB::raw("post_count + {$this->thread->approvedPostCount}"),
            'newest_thread_id' => max($this->thread->id, $category->newest_thread_id),
            'latest_active_thread_id' => $category->getLatestActiveThreadId(),
        ]);

        return $this->thread;
    }
}
