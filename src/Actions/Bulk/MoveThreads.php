<?php

namespace TeamTeaTime\Forum\Actions\Bulk;

use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Actions\BaseAction;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class MoveThreads extends BaseAction
{
    private array $threadIds;
    private Category $destinationCategory;
    private bool $includeTrashed;

    public function __construct(array $threadIds, Category $destinationCategory, bool $includeTrashed)
    {
        $this->threadIds = $threadIds;
        $this->destinationCategory = $destinationCategory;
        $this->includeTrashed = $includeTrashed;
    }

    protected function transact()
    {
        // Don't include threads that are already in the destination category
        $query = Thread::where('category_id', '!=', $this->destinationCategory->id)->whereIn('id', $this->threadIds);

        $threads = $this->includeTrashed
            ? $query->withTrashed()->get()
            : $query->get();

        // Return early if there are no eligible threads in the selection
        if ($threads->count() == 0) {
            return null;
        }

        $threadsByCategory = $threads->groupBy('category_id');
        $sourceCategories = $threads->pluck('category');
        $destinationCategory = $this->destinationCategory;

        $query->update(['category_id' => $destinationCategory->id]);

        foreach ($sourceCategories as $category) {
            $categoryThreads = $threadsByCategory->get($category->id);
            $threadCount = $categoryThreads->count();
            $postCount = $threadCount + $categoryThreads->sum('reply_count');
            $category->updateWithoutTouch([
                'newest_thread_id' => $category->getNewestThreadId(),
                'latest_active_thread_id' => $category->getLatestActiveThreadId(),
                'thread_count' => DB::raw("thread_count - {$threadCount}"),
                'post_count' => DB::raw("post_count - {$postCount}"),
            ]);
        }

        $threadCount = $threads->count();
        $postCount = $threads->count() + $threads->sum('reply_count');
        $destinationCategory->updateWithoutTouch([
            'newest_thread_id' => max($threads->max('id'), $destinationCategory->newest_thread_id),
            'latest_active_thread_id' => $destinationCategory->getLatestActiveThreadId(),
            'thread_count' => DB::raw("thread_count + {$threadCount}"),
            'post_count' => DB::raw("post_count + {$postCount}"),
        ]);

        return $threads;
    }
}
