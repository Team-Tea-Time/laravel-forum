<?php namespace Riari\Forum\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;
use Riari\Forum\Forum;

abstract class BaseModel extends Model
{
    /**
     * Create a new model instance.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->forceDeleting = !config('forum.preferences.misc.soft_delete');
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    public function getPostedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getUpdatedAttribute()
    {
        return $this->updated_at->diffForHumans();
    }

    public function getDeletedAttribute()
    {
        return !is_null($this->deleted_at) ? 1 : 0;
    }

    protected function rememberAttribute($item, $function)
    {
        $cacheItem = get_class($this).$this->id.$item;
        $value = Cache::remember($cacheItem, config('forum.preferences.cache.lifetime'), $function);
        return $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Determine if this model has been updated since the given model.
     *
     * @param  Model  $model
     * @return boolean
     */
    public function updatedSince(&$model)
    {
        return ($this->updated_at > $model->updated_at);
    }

    /**
     * Determine if this model has been updated.
     *
     * @return boolean
     */
    public function wasUpdated()
    {
        return ($this->updated_at > $this->created_at);
    }

    /**
     * Return an array of components used to construct this model's route.
     *
     * @return array
     */
    protected function getRouteComponents()
    {
        $components = [];
        return $components;
    }

    /**
     * Return a route of the given name using the current and specified route
     * components.
     *
     * @param  string  $name
     * @param  array  $components
     * @return string
     */
    protected function getRoute($name, $components = array())
    {
        return route($name, array_merge($this->getRouteComponents(), $components));
    }

    /**
     * Toggle an attribute on this model.
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
