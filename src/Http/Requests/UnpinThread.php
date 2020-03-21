<?php

namespace TeamTeaTime\Forum\Http\Requests;

class UnpinThread extends PinThread
{
    public function fulfill()
    {
        $thread = $this->route('thread');
        $thread->pinned = false;
        $thread->saveWithoutTouch();

        return $thread;
    }
}
