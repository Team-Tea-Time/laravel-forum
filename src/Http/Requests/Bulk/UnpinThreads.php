<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use TeamTeaTime\Forum\Events\UserBulkUnpinnedThreads;

class UnpinThreads extends PinThreads
{
    public function fulfill()
    {
        $this->threads()->update(['pinned' => false]);

        $threads = $this->threads()->get();

        event(new UserBulkUnpinnedThreads($this->user(), $threads));

        return $threads;
    }
}
