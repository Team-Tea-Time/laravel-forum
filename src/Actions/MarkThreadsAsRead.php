<?php

namespace TeamTeaTime\Forum\Actions;

use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class MarkThreadsAsRead extends BaseAction
{
    private ?Category $category;

    public function __construct(?Category $category)
    {
        $this->category = $category;
    }

    protected function transact()
    {
        $threads = Thread::recent();

        if (! is_null($this->category))
        {
            $threads = $threads->where('category_id', $this->category->id);
        }

        $threads = $threads->get()->filter(function ($thread)
        {
            return $thread->userReadStatus != null
                && (! $thread->category->is_private || $this->user()->can('view', $thread->category));
        });

        foreach ($threads as $thread)
        {
            $thread->markAsRead($this->user()->getKey());
        }

        return $threads;
    }
}