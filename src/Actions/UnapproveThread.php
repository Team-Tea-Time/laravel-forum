<?php

namespace TeamTeaTime\Forum\Actions;

use TeamTeaTime\Forum\Models\Thread;

class UnapproveThread extends BaseAction
{
    private Thread $thread;

    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }

    protected function transact()
    {
        if (!$this->thread->isApproved) {
            return null;
        }

        $this->thread->updateWithoutTouch([
            'approved_at' => null,
        ]);

        $this->thread->firstPost->updateWithoutTouch([
            'approved_at' => null,
        ]);

        $category = $this->thread->category;

        $attributes = [];
        if ($category->newest_thread_id === $this->thread->id) {
            $attributes['newest_thread_id'] = $category->getNewestThreadId();
        }
        if ($category->latest_active_thread_id === $this->thread->id) {
            $attributes['latest_active_thread_id'] = $category->getLatestActiveThreadId();
        }

        $category->update($attributes);

        return $this->thread;
    }
}
