<?php namespace Atrakeur\Forum\Repositories;

use \stdClass;
use \Eloquent;
use \Illuminate\Database\Eloquent\Collection;

abstract class AbstractBaseRepository  {

	protected $model;

	protected function getFirstBy($index, $value, array $with = array())
	{
		$model = $this->model->where($index, '=', $value)->with($with)->first();
		return $model->toObject();
	}

	protected function getManyBy($index, $value, array $with = array())
	{
		$model = $this->model->where($index, '=', $value)->with($with)->get();
		return $model->toObject();
	}

}
