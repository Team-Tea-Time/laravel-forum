<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\{
    Actions\UnapproveThread as Action,
    Events\UserUnapprovedThread,
};

class UnapproveThread extends ApproveThread
{
    public function fulfill()
    {
        $action = new Action($this->route('thread'));
        $thread = $action->execute();

        UserUnapprovedThread::dispatch($this->user(), $thread);

        return $thread;
    }
}
