<?php namespace Atrakeur\Forum\Repositories;

use \stdClass;
use \Eloquent;
use \Illuminate\Database\Eloquent\Collection;

abstract class AbstractBaseRepository  {

	protected $model;

	protected function getFirstBy($index, $value, array $with = array())
	{
		$model = $this->model->where($index, '=', $value)->with($with)->first();
		return $this->toObject($model);
	}

	protected function getManyBy($index, $value, array $with = array())
	{
		$model = $this->model->where($index, '=', $value)->with($with)->get();
		return $this->toObject($model);
	}

	protected function toObject($value)
	{
		if ($value instanceof Eloquent)
		{
			$attributes = $value->toArray();
			$object = new stdClass();
			foreach($attributes AS $key => $attribute)
			{
				$object->$key = $this->toObject($value->$key);
			}
			return $object;
		}
		
		if ($value instanceof Collection)
		{
			$array = array();
			foreach($value AS $key => $element)
			{
				$array[$key] = $this->toObject($element);
			}
			return $array;
		}

		return $value;
	}

}
