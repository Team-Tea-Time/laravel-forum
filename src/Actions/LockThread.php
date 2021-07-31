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
        $this->thread->updateWithoutTouch([
            'locked' => true,
        ]);

        return $this->thread;
    }
}
