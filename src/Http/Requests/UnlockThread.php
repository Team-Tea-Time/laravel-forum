<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\Events\UserUnlockedThread;

class UnlockThread extends LockThread
{
    public function fulfill()
    {
        $thread = $this->route('thread');
        $thread->locked = false;
        $thread->saveWithoutTouch();

        event(new UserUnlockedThread($this->user(), $thread));

        return $thread;
    }
}
