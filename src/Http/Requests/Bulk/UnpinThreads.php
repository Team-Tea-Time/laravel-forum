<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Http\Requests\Traits\BulkQueriesThreads;

class UnpinThreads extends PinThreads
{
    use AuthorizesAfterValidation, BulkQueriesThreads;

    public function fulfill()
    {
        return $this->threads()->update(['pinned' => false]);
    }
}
