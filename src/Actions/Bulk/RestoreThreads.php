<?php

namespace TeamTeaTime\Forum\Actions\Bulk;

use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Actions\BaseAction;
use TeamTeaTime\Forum\Models\Thread;

class RestoreThreads extends BaseAction
{
    private array $threadIds;

    public function __construct(array $threadIds)
    {
        $this->threadIds = $threadIds;
    }

    protected function transact()
    {
        $query = Thread::whereIn('id', $this->threadIds)->onlyTrashed();

        // Return early if there are no eligible threads in the selection
        if ($query->count() == 0) {
            return null;
        }

        // Fetch the approved subset of the threads so we can operate on the affected categories below
        $threads = $query->approved()->get();

        $rowsAffected = Thread::withoutTimestamps(fn () => $query->restore());

        if ($rowsAffected == 0) {
            return null;
        }

        $threadsByCategory = $threads->groupBy('category_id');
        foreach ($threadsByCategory as $categoryThreads) {
            $threadCount = $categoryThreads->count();
            $postCount = $categoryThreads->sum('reply_count') + $threadCount; // count the first post of each thread
            $category = $threads->first()->category;

            $category->update([
                'newest_thread_id' => max($threads->max('id'), $category->newest_thread_id),
                'latest_active_thread_id' => $category->getLatestActiveThreadId(),
                'thread_count' => DB::raw("thread_count + {$threadCount}"),
                'post_count' => DB::raw("post_count + {$postCount}"),
            ]);
        }

        return $threads;
    }
}
