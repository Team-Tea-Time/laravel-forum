<?php namespace Riari\Forum\Models;

use Auth;
use Cache;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
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

    protected function rememberAttribute($item, $function)
    {
        $cacheItem = get_class($this).$this->id.$item;

        $value = Cache::remember($cacheItem, config('forum.preferences.cache.lifetime'), $function);

        return $value;
    }

    protected static function clearAttributeCache($model)
    {
        foreach ($model->appends as $attribute) {
            $cacheItem = get_class($model).$model->id.$attribute;
            Cache::forget($cacheItem);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    // Returns true if this model has been updated since the given model
    public function updatedSince(&$model)
    {
        return ($this->updated_at > $model->updated_at);
    }

    // Returns permission check for the specified permission (route name)
    protected function userCan($permission)
    {
        return permitted($this->getAccessParams(), $permission, Auth::user());
    }

    // Returns access parameters for checking access
    protected function getAccessParams()
    {
        $parameters = [];
        return $parameters;
    }

    // Returns route components for building routes
    protected function getRouteComponents()
    {
        $components = [];
        return $components;
    }

    // Returns a route using the currently set route components
    protected function getRoute($name, $components = array())
    {
        return route($name, array_merge($this->getRouteComponents(), $components));
    }

    // Toggles an attribute on the model and saves it
    public function toggle($property)
    {
        $this->$property = !$this->$property;
        $this->save();
    }
}
