<?php namespace Eorzea\Forum\Repositories;

use stdClass;
use Config;

abstract class AbstractBaseRepository {

	protected $model;

	protected $itemsPerPage = 0;

	protected function getFirstBy($index, $value, array $with = array())
	{
		$model = $this->model->where($index, '=', $value)->with($with)->first();
		return $model;
	}

	protected function getManyBy($index, $value, array $with = array())
	{
		$model = $this->model->where($index, '=', $value)->with($with);

		if ($this->itemsPerPage != 0)
		{
			$model = $model->paginate($this->itemsPerPage);
		}
		else
		{
			$model = $model->get();
		}

		return $model;
	}

	public function getPaginationLinks($index, $value)
	{
		return $this->model->where($index, '=', $value)->paginate($this->itemsPerPage)->links(Config::get('forum::integration.pagination_view'));
	}

	public function create(Array $data = array())
	{
		$model = $this->model->create($data);

		return $model;
	}

	public function update(Array $data = array())
	{
		$model = $this->model->find($data['id']);
		if ($model != null)
		{
			$model->fill($data);
			$model->save();

			return $model;
		}
		else
		{
			throw new InvalidArgumentException('Data must contain an existing id to update');
		}
	}

}
