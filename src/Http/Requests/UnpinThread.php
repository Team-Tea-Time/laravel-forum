<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\Events\UserUnpinnedThread;

class UnpinThread extends PinThread
{
    public function fulfill()
    {
        $thread = $this->route('thread');
        $thread->pinned = false;
        $thread->saveWithoutTouch();

        event(new UserUnpinnedThread($this->user(), $thread));

        return $thread;
    }
}
