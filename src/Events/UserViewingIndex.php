<?php

namespace TeamTeaTime\Forum\Events;

class UserViewingIndex
{
    /** @var mixed */
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}
