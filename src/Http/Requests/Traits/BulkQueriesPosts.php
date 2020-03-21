<?php

namespace TeamTeaTime\Forum\Http\Requests\Traits;

use Illuminate\Database\Eloquent\Builder;
use TeamTeaTime\Forum\Models\Post;

trait BulkQueriesThreads
{
    private function threads(): Builder
    {
        $query = $this->user()->can('viewTrashedThreads') ? Post::withTrashed() : Post::query();
        return $query->whereIn('id', $this->validated()['posts']);
    }
}