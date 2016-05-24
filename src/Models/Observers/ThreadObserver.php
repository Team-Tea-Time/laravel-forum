<?php namespace Riari\Forum\Models\Observers;

use Carbon\Carbon;

class ThreadObserver
{
    public function deleted($thread)
    {
        // Delete the thread's posts
        if ($thread->deleted_at != Carbon::now()) {
            // The thread was force-deleted, so the posts should be too
            $thread->posts()->withTrashed()->forceDelete();

            // Also detach readers
            $thread->readers()->detach();
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
