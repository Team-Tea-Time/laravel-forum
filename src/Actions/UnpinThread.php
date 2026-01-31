<?php

namespace TeamTeaTime\Forum\Actions;

use TeamTeaTime\Forum\Models\Thread;

class UnpinThread extends BaseAction
{
    private Thread $thread;

    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }

    protected function transact()
    {
        if (!$this->thread->pinned) {
            return null;
        }

        Thread::withoutTimestamps(fn () => $this->thread->update(['pinned' => false]));

        return $this->thread;
    }
}
