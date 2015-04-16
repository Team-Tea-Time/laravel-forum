<?php namespace Riari\Forum\Models;

use Cache;
use Carbon\Carbon;
use Config;
use Eloquent;

abstract class BaseModel extends Eloquent {

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    public function getPostedAttribute()
    {
        return $this->getTimeAgo($this->created_at);
    }

    public function getUpdatedAttribute()
    {
        return $this->getTimeAgo($this->updated_at);
    }

    protected function rememberAttribute($item, $function)
    {
        $cacheItem = get_class($this).$this->id.$item;

        $value = Cache::remember($cacheItem, Config::get('forum::preferences.cache_lifetime'), $function);

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

    // Returns route components for building routes
    protected function getRouteComponents()
    {
        $components = array();
        return $components;
    }

    // Returns a route using the current set route components
    protected function getRoute($name, $components = array())
    {
        return route($name, array_merge($this->getRouteComponents(), $components));
    }

    // Returns a human readable diff of the given timestamp
    protected function getTimeAgo($timestamp)
    {
        return Carbon::createFromTimeStamp(strtotime($timestamp))->diffForHumans();
    }

    // Toggles a property (column) on the model and saves it
    public function toggle($property)
    {
        $this->$property = !$this->$property;
        $this->save();
    }

}
