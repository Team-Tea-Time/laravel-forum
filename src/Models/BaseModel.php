<?php

namespace Riari\Forum\Models;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Riari\Forum\Forum;

abstract class BaseModel extends Model
{
    /**
     * @var Router
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
        $this->forceDeleting = !config('forum.preferences.soft_deletes');
        $this->router = App::make('Illuminate\Routing\Router');
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
     * Helper: Build a named route using the parameters set by the model.
     *
     * @param  string  $name
     * @param  array  $extraParameters
     * @return string
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
