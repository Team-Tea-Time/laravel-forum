<?php

namespace TeamTeaTime\Forum\Actions;

use TeamTeaTime\Forum\Models\Thread;

class RenameThread extends BaseAction
{
    private Thread $thread;
    private string $title;

    public function __construct(Thread $thread, string $title)
    {
        $this->thread = $thread;
        $this->title = $title;
    }

    protected function transact()
    {
        Thread::withoutTimestamps(fn () => $this->thread->update(['title' => $this->title]));

        return $this->thread;
    }
}
