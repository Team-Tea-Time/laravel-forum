<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\{
    Actions\UnlockThread as Action,
    Events\UserUnlockedThread,
};

class UnlockThread extends LockThread
{
    public function fulfill()
    {
        $action = new Action($this->route('thread'));
        $thread = $action->execute();

        UserUnlockedThread::dispatch($this->user(), $thread);

        return $thread;
    }
}
