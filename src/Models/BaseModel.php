<?php

namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class BaseModel extends Model
{
    /**
     * @var Router
     *
     * @todo remove before 3.1.0
     */
    protected $router;

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

        // @todo remove before 3.1.0
        $this->router = App::make('Illuminate\Routing\Router');
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
     * Helper: Build a named route using the parameters set by the model.
     *
     * @param  string  $name
     * @param  array  $extraParameters
     * @return string
     *
     * @deprecated as of 3.0.2
     * @todo remove before 3.1.0
     */
    public function buildRoute($name, $extraParameters = [])
    {
        $parameterNames = array_flip($this->router->getRoutes()->getByName($name)->parameterNames());
        $parameters = array_intersect_key($this->getRouteParameters(), $parameterNames);

        return route($name, array_merge($parameters, $extraParameters));
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
}
