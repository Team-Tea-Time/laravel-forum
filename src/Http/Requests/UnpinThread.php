<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\{
    Actions\UnpinThread as Action,
    Events\UserUnpinnedThread,
};

class UnpinThread extends PinThread
{
    public function fulfill()
    {
        $action = new Action($this->route('thread'));
        $thread = $action->execute();

        UserUnpinnedThread::dispatch($this->user(), $thread);

        return $thread;
    }
}
