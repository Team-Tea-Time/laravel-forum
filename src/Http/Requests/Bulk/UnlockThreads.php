<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

class UnlockThreads extends LockThreads
{
    public function fulfill()
    {
        return $this->threads()->update(['locked' => false]);
    }
}
