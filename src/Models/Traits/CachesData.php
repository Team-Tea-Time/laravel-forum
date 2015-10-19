<?php

namespace Riari\Forum\Models\Traits;

use Cache;
use ReflectionClass;

trait CachesData
{
    /**
     * Helper: Cache a model attribute/value.
     *
     * @param  string  $key
     * @param  callable  $function
     * @return string
     */
    protected function remember($key, callable $function)
    {
        $class = new ReflectionClass($this);
        $class = $class->getShortName();

        $lifetimes = config('forum.preferences.cache_lifetimes');
        $lifetime = ($lifetimes[$class][$key]) ? $lifetimes[$class][$key] : $lifetimes['default'];

        return Cache::remember($class.$this->id.$key, $lifetime, $function);
    }
}
