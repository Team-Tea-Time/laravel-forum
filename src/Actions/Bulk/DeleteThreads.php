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

        if ($this->includeTrashed) {
            $threads = $query->withTrashed()->get();

            // Return early if this is a soft-delete and the selected threads are already trashed,
            // or there are no valid threads in the selection
            if (!$this->permaDelete && $threads->whereNull('deleted_at')->count() == 0) {
                return null;
            }
        } else {
            $threads = $query->get();

            // Return early if there are no valid threads in the selection
            if ($threads->count() == 0) {
                return null;
            }
        }

        // Use the raw query builder to prevent touching updated_at
        $query = DB::table(Thread::getTableName())->whereIn('id', $this->threadIds);

        if ($this->permaDelete) {
            $rowsAffected = $query->delete();

            // Drop readers for the removed threads
            DB::table(Thread::READERS_TABLE)->whereIn('thread_id', $this->threadIds)->delete();
        } else {
            $rowsAffected = $query->whereNull('deleted_at')->update(['deleted_at' => DB::raw('now()')]);
        }

        if ($rowsAffected == 0) {
            return null;
        }

        $threadsByCategory = $threads->groupBy('category_id');
        foreach ($threadsByCategory as $categoryThreads) {
            // Count only non-deleted threads for changes to category stats since soft-deleted threads
            // are already represented
            $threadCount = $categoryThreads->whereNull('deleted_at')->count();

            // Sum of reply counts + thread count = total posts
            $postCount = $categoryThreads->whereNull('deleted_at')->sum('reply_count') + $threadCount;

            $category = $categoryThreads->first()->category;

            $updates = [
                'newest_thread_id' => $category->getNewestThreadId() ?? 0,
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

        if ($this->permaDelete) {
            Post::whereIn('thread_id', $this->threadIds)->withTrashed()->forceDelete();
        }

        return $threads;
    }
}
