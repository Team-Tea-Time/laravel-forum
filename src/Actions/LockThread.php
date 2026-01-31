<?php

namespace TeamTeaTime\Forum\Actions;

use TeamTeaTime\Forum\Models\Thread;

class LockThread extends BaseAction
{
    private Thread $thread;

    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }

    protected function transact()
    {
        if ($this->thread->locked) {
            return null;
        }

        Thread::withoutTimestamps(fn () => $this->thread->update(['locked' => true]));

        return $this->thread;
    }
}
