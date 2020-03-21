<?php

namespace TeamTeaTime\Forum\Events\Types;

use TeamTeaTime\Forum\Models\Thread;

class ThreadEvent
{
    /** @var mixed */
    public $user;

    /** @var Thread */
    public $thread;

    public function __construct($user, Thread $thread)
    {
        $this->user = $user;
        $this->thread = $thread;
    }
}
