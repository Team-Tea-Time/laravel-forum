<?php

namespace TeamTeaTime\Forum\Actions\Bulk;

use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\{
    Actions\BaseAction,
    Models\Post,
    Models\Thread,
};

class DeleteThreads extends BaseAction
{
    private array $threadIds;
    private bool $includeTrashed;
    private bool $permaDelete;

    public function __construct(array $threadIds, bool $includeTrashed, bool $permaDelete = false)
    {
        $this->threadIds = $threadIds;
        $this->includeTrashed = $includeTrashed;
        $this->permaDelete = $permaDelete;
    }

    protected function transact()
    {
        $query = Thread::whereIn('id', $this->threadIds);

        if ($this->permaDelete && $this->includeTrashed) {
            $query = $query->withTrashed();
        }

        if ($query->count() == 0 || ($this->permaDelete && $query->notDeleted()->count() == 0)) {
            return null;
        }

        // Fetch the approved, non-deleted subset of the threads so we can operate on the affected
        // categories below
        $threads = (clone $query)->approved()->notDeleted()->get();

        if ($this->permaDelete) {
            $query->forceDelete();

            Post::whereIn('thread_id', $this->threadIds)->withTrashed()->forceDelete();

            // Drop readers for the removed threads
            DB::table(Thread::READERS_TABLE)->whereIn('thread_id', $this->threadIds)->delete();
        } else {
            Thread::withoutTimestamps(fn () => $query->delete());

            // Note: in order to preserve the deletion state of posts in case any of the threads
            // are restored, we skip soft-deletion of posts here.
        }

        if ($threads->count() == 0) {
            // We only dealt with unapproved and/or soft-deleted threads, so no category update is
            // necessary
            return $threads;
        }

        $threadsByCategory = $threads->groupBy('category_id');
        foreach ($threadsByCategory as $categoryThreads) {
            $threadCount = $categoryThreads->count();

            // Sum of reply counts + thread count = total posts
            $postCount = $categoryThreads->sum('reply_count') + $threadCount;

            $category = $categoryThreads->first()->category;

            $updates = [
                'newest_thread_id' => $category->getNewestThreadId(),
                'latest_active_thread_id' => $category->getLatestActiveThreadId(),
            ];

            if ($threadCount > 0) {
                $updates['thread_count'] = DB::raw("thread_count - {$threadCount}");
            }

            if ($postCount > 0) {
                $updates['post_count'] = DB::raw("post_count - {$postCount}");
            }

            $category->update($updates);
        }

        return $threads;
    }
}
