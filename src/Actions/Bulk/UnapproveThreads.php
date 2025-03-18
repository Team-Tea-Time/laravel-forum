<?php

namespace TeamTeaTime\Forum\Actions\Bulk;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\{
    Actions\BaseAction,
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
        $threads = Thread::whereIn('id', $this->threadIds)->get();

        if ($threads->count() == 0) {
            return null;
        }

        // Use the raw query builder to prevent touching updated_at
        $query = DB::table(Thread::getTableName())->whereIn('id', $this->threadIds);
        $rowsAffected = $query->where('approved_at', '<=', Carbon::now()->toDateTimeString())
            ->update(['approved_at' => null]);

        if ($rowsAffected == 0) {
            return null;
        }

        return $threads;
    }
}
