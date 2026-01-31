<?php

namespace TeamTeaTime\Forum\Actions\Bulk;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\{
    Actions\BaseAction,
    Models\Category,
    Models\Post,
    Models\Thread,
};

class UnapproveThreads extends BaseAction
{
    private array $threadIds;

    public function __construct(array $threadIds)
    {
        $this->threadIds = $threadIds;
    }

    protected function transact()
    {
        $query = Thread::whereIn('id', $this->threadIds)
            ->notDeleted()
            ->approved();

        if ($query->count() == 0) {
            return null;
        }

        // Fetch the threads so we can operate on affected categories below
        $threads = $query->with('category')->get();

        Thread::withoutTimestamps(fn () => $query->update(['approved_at' => null]));

        $categories = $threads->pluck('category')->unique()->values();
        foreach ($categories as $category) {
            $threadsInCategory = $threads->where('category_id', $category->id);
            $postCount = 0;

            foreach ($threadsInCategory as $thread) {
                $postCount += $thread->approvedPostCount;

                Post::withoutTimestamps(fn () => $thread->firstPost()->update([
                    'approved_at' => null
                ]));
            }

            $category->update([
                'thread_count' => DB::raw("thread_count - {$threadsInCategory->count()}"),
                'post_count' => DB::raw("post_count - {$postCount}"),
                'newest_thread_id' => $category->getNewestThreadId(),
                'latest_active_thread_id' => $category->getLatestActiveThreadId()
            ]);
        }

        return $threads;
    }
}
