<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use TeamTeaTime\Forum\Actions\Bulk\UnapproveThreads as Action;
use TeamTeaTime\Forum\Events\UserBulkUnapprovedThreads;

class UnapproveThreads extends ApproveThreads
{
    public function fulfill()
    {
        $action = new Action($this->validated()['threads']);
        $threads = $action->execute();

        if ($threads !== null) {
            UserBulkUnapprovedThreads::dispatch($this->user(), $threads);
        }

        return $threads;
    }
}
