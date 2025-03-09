<?php

namespace TeamTeaTime\Forum\Actions\Bulk;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\{
    Actions\BaseAction,
    Models\BaseModel,
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
        $threads = Thread::whereIn('id', $this->threadIds)->get();

        if ($threads->count() == 0) {
            return null;
        }

        // Use the raw query builder to prevent touching updated_at
        $query = DB::table(Thread::getTableName())->whereIn('id', $this->threadIds);
        $rowsAffected = $query->whereNull(BaseModel::APPROVED_AT)
            ->orWhere(BaseModel::APPROVED_AT, '>', Carbon::now()->toDateTimeString())
            ->update([BaseModel::APPROVED_AT => DB::raw('now()')]);

        if ($rowsAffected == 0) {
            return null;
        }

        return $threads;
    }
}
