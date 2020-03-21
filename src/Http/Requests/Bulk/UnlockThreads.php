<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Http\Requests\Traits\BulkQueriesThreads;

class UnlockThreads extends LockThreads
{
    use AuthorizesAfterValidation, BulkQueriesThreads;

    public function fulfill()
    {
        return $this->threads()->update(['locked' => false]);
    }
}
