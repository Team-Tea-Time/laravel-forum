<?php

namespace TeamTeaTime\Forum\Http\Requests\Traits;

use Illuminate\Database\Eloquent\Builder;
use TeamTeaTime\Forum\Models\Thread;

trait BulkQueriesThreads
{
    private function threads(): Builder
    {
        $query = $this->user()->can('viewTrashedThreads') ? Thread::withTrashed() : Thread::query();
        return $query->whereIn('id', $this->validated()['threads']);
    }
}