<?php

namespace Riari\Forum\Models\Observers;

class ThreadObserver extends BaseObserver
{
    public function deleted($thread)
    {
        // Delete the thread's posts
        if ($thread->deleted_at != $this->carbon->now()) {
            // The thread was force-deleted, so the posts should be too
            $thread->posts()->withTrashed()->forceDelete();
        } else {
            // The thread was soft-deleted, so just soft-delete its posts
            $thread->posts()->delete();
        }
    }

    public function restored($thread)
    {
        // Restore the thread's posts
        $thread->posts()->withTrashed()->restore();
    }
}
