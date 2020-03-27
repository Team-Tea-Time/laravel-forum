<?php namespace TeamTeaTime\Forum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

abstract class BaseModel extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($this->forceDeleting)
        {
            $this->forceDeleting = ! config('forum.preferences.soft_deletes');
        }
    }

    public function scopeRequestWhere(Builder $query, Request $request): Builder
    {
        if ($request->has('where'))
        {
            $query->where($request->input('where'));
        }

        return $query;
    }

    public function scopeRequestWith(Builder $query, Request $request): Builder
    {
        if ($request->has('with'))
        {
            $query->with($request->input('with'));
        }

        return $query;
    }

    public function scopeRequestAppend(Builder $query, Request $request): Builder
    {
        if ($request->has('append'))
        {
            $query->append($request->input('append'));
        }

        return $query;
    }

    public function scopeRequestOrder(Builder $query, Request $request): Builder
    {
        if ($request->has('orderBy'))
        {
            $direction = ($request->has('orderDir')) ? $request->input('orderDir') : 'desc';
            $query->orderBy($request->input('orderBy'), $direction);
        }

        return $query;
    }

    public function withRequestScopes(Request $request): Builder
    {
        return $this->requestWhere($request)->requestWith($request)->requestAppend($request)->requestOrder($request);
    }

    public function updatedSince(Model &$model): bool
    {
        return ($this->updated_at > $model->updated_at);
    }

    public function hasBeenUpdated(): bool
    {
        return ($this->updated_at > $this->created_at);
    }

    public function saveWithoutTouch()
    {
        $this->timestamps = false;
        $this->save();
        $this->timestamps = true;
    }

    public function deleteWithoutTouch()
    {
        $this->timestamps = false;
        $this->delete();
        $this->timestamps = true;
    }

    public function forceDeleteWithoutTouch()
    {
        $this->timestamps = false;
        $this->forceDelete();
        $this->timestamps = true;
    }

    public function restoreWithoutTouch()
    {
        $this->timestamps = false;
        $this->restore();
        $this->timestamps = true;
    }
}
