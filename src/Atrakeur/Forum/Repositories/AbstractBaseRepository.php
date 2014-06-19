<?php namespace Atrakeur\Forum\Repositories;

abstract class AbstractBaseRepository  {

	protected $model;

	protected function getFirstBy($index, $value, array $with = array())
	{
		$model = $this->model->where($index, '=', $value)->with($with)->first();
		return $this->model->convertToObject($model);
	}

	protected function getManyBy($index, $value, array $with = array())
	{
		$model = $this->model->where($index, '=', $value)->with($with)->get();
		return $this->model->convertToObject($model);
	}

}
