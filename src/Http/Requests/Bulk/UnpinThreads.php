<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

class UnpinThreads extends PinThreads
{
    public function fulfill()
    {
        return $this->threads()->update(['pinned' => false]);
    }
}
