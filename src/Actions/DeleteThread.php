<?php

namespace TeamTeaTime\Forum\Actions;

use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Models\Thread;

class DeleteThread extends BaseAction
{
    private Thread $thread;
    private bool $permaDelete;

    public function __construct(Thread $thread, bool $permaDelete = false)
    {
        $this->thread = $thread;
        $this->permaDelete = $permaDelete;
    }

    protected function transact()
    {
        $threadAlreadyTrashed = $this->thread->trashed();
        $postsRemoved = 0;

        if ($this->permaDelete) {
            $this->thread->readers()->detach();
            $this->thread->posts()->withTrashed()->forceDelete();
            $this->thread->forceDelete();

            $postsRemoved = $this->thread->postCount;
        } else {
            // Return early if the thread was already trashed because there's nothing to do
            if ($threadAlreadyTrashed) {
                return null;
            }

            $this->thread->readers()->detach();
            $this->thread->deleteWithoutTouch();
        }

        // Only update category stats and FKs if the thread wasn't already soft-deleted,
        // otherwise they'll needlessly be updated a second time
        if (! $threadAlreadyTrashed) {
            $attributes = [
                'thread_count' => DB::raw('thread_count - 1'),
            ];

            if ($postsRemoved) {
                $attributes['post_count'] = DB::raw("post_count - {$postsRemoved}");
            }

            $category = $this->thread->category;

            if ($category->newest_thread_id === $this->thread->id) {
                $attributes['newest_thread_id'] = $category->getNewestThreadId();
            }
            if ($category->latest_active_thread_id === $this->thread->id) {
                $attributes['latest_active_thread_id'] = $category->getLatestActiveThreadId();
            }

            $category->update($attributes);
        }

        return $this->thread;
    }
}
