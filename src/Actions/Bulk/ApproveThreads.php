<?php

namespace TeamTeaTime\Forum\Actions\Bulk;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\{
    Actions\BaseAction,
    Models\Category,
    Models\Thread,
};

class ApproveThreads extends BaseAction
{
    private array $threadIds;

    public function __construct(array $threadIds)
    {
        $this->threadIds = $threadIds;
    }

    protected function transact()
    {
        $threads = Thread::whereIn('id', $this->threadIds)
            ->notDeleted()
            ->pendingApproval()
            ->get();

        if ($threads->count() == 0) {
            return null;
        }

        // We only want to execute the action on the valid subset of the selection
        $eligibleThreadIds = $threads->pluck('id');

        // Use the raw query builder to prevent touching updated_at
        $query = DB::table(Thread::getTableName())->whereIn('id', $eligibleThreadIds);
        $rowsAffected = $query->whereNull('approved_at')
            ->orWhere('approved_at', '>', Carbon::now()->toDateTimeString())
            ->update(['approved_at' => DB::raw('now()')]);

        if ($rowsAffected == 0) {
            return null;
        }

        $categoryIds = $threads->pluck('category_id');
        $categories = Category::whereIn('id', $categoryIds)->get();
        foreach ($categories as $category) {
            $category->update([
                'newest_thread_id' => $category->getNewestThreadId() ?? 0,
                'latest_active_thread_id' => $category->getLatestActiveThreadId(),
            ]);
        }

        return $threads;
    }
}
