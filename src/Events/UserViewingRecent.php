<?php

namespace TeamTeaTime\Forum\Events;

use Illuminate\Database\Eloquent\Collection;

class UserViewingNew
{
    /** @var Collection */
    public $threads;

    public function __construct(Collection $threads)
    {
        $this->threads = $threads;
    }
}
