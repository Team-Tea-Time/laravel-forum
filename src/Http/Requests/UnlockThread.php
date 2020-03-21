<?php

namespace TeamTeaTime\Forum\Http\Requests;

class UnlockThread extends LockThread
{
    public function fulfill()
    {
        $thread = $this->route('thread');
        $thread->locked = false;
        $thread->save();

        return $thread;
    }
}
