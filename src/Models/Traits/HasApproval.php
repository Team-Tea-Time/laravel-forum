<?php

namespace TeamTeaTime\Forum\Models\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User;

trait HasApproval
{
    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereNotNull('approved_at')->where('approved_at', '<=', Carbon::now());
    }

    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->whereNull('approved_at')->orWhere('approved_at', '>', Carbon::now());
    }

    public function scopeAuthoredByOrApproved(Builder $query, ?User $user): Builder
    {
        if ($user === null) return $query->approved();

        return $query->where('author_id', $user->getKey())
            ->orWhere(fn ($query) => $query->approved());
    }

    protected function isApproved(): Attribute
    {
        return new Attribute(
            get: function ()
            {
                return $this->approved_at != null && $this->approved_at < Carbon::now();
            }
        );
    }
}
