<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use TeamTeaTime\Forum\Events\UserBulkUnlockedThreads;

class UnlockThreads extends LockThreads
{
    public function fulfill()
    {
        $this->threads()->update(['locked' => false]);

        $threads = $this->threads()->get();

        event(new UserBulkUnlockedThreads($this->user(), $threads));

        return $threads;
    }
}
