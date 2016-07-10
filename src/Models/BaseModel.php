<?php namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class BaseModel extends Model
{
    /**
     * Create a new model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($this->forceDeleting) {
            $this->forceDeleting = !config('forum.preferences.soft_deletes');
        }
    }

    /**
     * Scope: Conditionally apply where() to the query based on the current request.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  Request  $request
     * @return Model
     */
    public function scopeRequestWhere($query, Request $request)
    {
        if ($request->has('where')) {
            $query->where($request->input('where'));
        }

        return $query;
    }

    /**
     * Scope: Conditionally apply with() to the query based on the current request.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  Request  $request
     * @return Model
     */
    public function scopeRequestWith($query, Request $request)
    {
        if ($request->has('with')) {
            $query->with($request->input('with'));
        }

        return $query;
    }

    /**
     * Scope: Conditionally apply append() to the query based on the current request.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  Request  $request
     * @return Model
     */
    public function scopeRequestAppend($query, Request $request)
    {
        if ($request->has('append')) {
            $query->append($request->input('append'));
        }

        return $query;
    }

    /**
     * Scope: Coditionally apply orderBy() to the query based on the current request.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  Request  $request
     * @return Model
     */
    public function scopeRequestOrder($query, Request $request)
    {
        if ($request->has('orderBy')) {
            $direction = ($request->has('orderDir')) ? $request->input('orderDir') : 'desc';
            $query->orderBy($request->input('orderBy'), $direction);
        }

        return $query;
    }

    /**
     * Attribute: "X ago" created date.
     *
     * @return string
     */
    public function getPostedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Attribute: "X ago" updated date.
     *
     * @return string
     */
    public function getUpdatedAttribute()
    {
        return $this->updated_at->diffForHumans();
    }

    /**
     * Helper: Apply request-based scopes to the model query.
     *
     * @param  Request  $request
     * @return Model
     */
    public function withRequestScopes(Request $request)
    {
        return $this->requestWhere($request)->requestWith($request)->requestAppend($request)->requestOrder($request);
    }

    /**
     * Helper: Determine if this model has been updated since the given model.
     *
     * @param  Model  $model
     * @return boolean
     */
    public function updatedSince(&$model)
    {
        return ($this->updated_at > $model->updated_at);
    }

    /**
     * Helper: Determine if this model has been updated.
     *
     * @return boolean
     */
    public function hasBeenUpdated()
    {
        return ($this->updated_at > $this->created_at);
    }

    /**
     * Helper: Toggle an attribute on this model.
     *
     * @param  string  $attribute
     * @return void
     */
    public function toggle($attribute)
    {
        $this->$attribute = !$this->$attribute;
        $this->save();
    }

    /**
     * Helper: save without touching updated_at.
     *
     * @return null
     */
    public function saveWithoutTouch()
    {
        $this->timestamps = false;
        $this->save();
        $this->timestamps = true;
    }
}
