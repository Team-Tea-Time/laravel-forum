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

        return $this->thread;
    }
}
