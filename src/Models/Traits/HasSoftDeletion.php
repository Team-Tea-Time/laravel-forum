<?php

namespace TeamTeaTime\Forum\Models\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait HasSoftDeletion
{
    public function scopeNotDeleted(Builder $query): Builder
    {
        return $query->whereNull('deleted_at')->orWhere('deleted_at', '>', Carbon::now());
    }
}
