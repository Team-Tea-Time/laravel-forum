<?php namespace Eorzea\Forum\Models;

use stdClass;
use Cache;
use Eloquent;

abstract class AbstractBaseModel extends Eloquent {

	protected function rememberAttribute($item, $function)
	{
		$cacheItem = get_class($this).$this->id.$item;

		$value = Cache::remember($cacheItem, 1, $function);

		return $value;
	}

	protected static function clearAttributeCache($model)
	{
		foreach ($model->appends as $attribute) {
			$cacheItem = get_class($model).$model->id.$attribute;
			Cache::forget($cacheItem);
		}
	}

}
