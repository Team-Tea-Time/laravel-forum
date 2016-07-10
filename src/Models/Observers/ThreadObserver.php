<?php namespace Riari\Forum\Models\Observers;

use Carbon\Carbon;
use Riari\Forum\Models\Thread;
use Riari\Forum\Support\Stats;

class ThreadObserver
{
    public function created($thread)
    {
        // Increment thread count on the category
        $thread->category->increment('thread_count');
    }

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

        Stats::updateCategory($thread->category);
    }

    public function restored($thread)
    {
        // Restore the thread's posts
        $thread->posts()->withTrashed()->restore();

        Stats::updateThread($thread);
        Stats::updateCategory($thread->category);
    }
}
