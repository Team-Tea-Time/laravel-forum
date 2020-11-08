<?php

namespace TeamTeaTime\Forum\Events;

use Illuminate\Database\Eloquent\Collection;

class UserViewingUnread
{
    /** @var mixed */
    public $user;

    public Collection $threads;

    public function __construct($user, Collection $threads)
    {
        $this->user = $user;
        $this->threads = $threads;
    }
}
