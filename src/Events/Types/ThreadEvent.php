<?php

namespace TeamTeaTime\Forum\Events\Types;

use TeamTeaTime\Forum\Models\Thread;

class ThreadEvent
{
    /** @var Thread */
    public $thread;

    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }
}
