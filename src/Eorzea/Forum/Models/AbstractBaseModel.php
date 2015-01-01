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

	public function toObject()
	{
		return $this->convertToObject($this);
	}

	public function convertToObject($value)
	{
		if ($value instanceof Eloquent)
		{
			$attributes = $value->toArray();
			$relations  = $value->relationsToArray();

			$object = new stdClass();
			foreach($attributes as $key => $attribute)
			{
				if (array_key_exists($key, $relations))
				{
					$key = camel_case($key);
					$object->$key = $this->convertToObject($value->$key);
				}
				else
				{
					$object->$key = $attribute;
				}
			}
			return $object;
		}

		if ($value instanceof \Illuminate\Database\Eloquent\Collection)
		{
			$array = array();
			foreach($value as $key => $element)
			{
				$array[$key] = $this->convertToObject($element);
			}
			return $array;
		}
		return $value;
	}

}
