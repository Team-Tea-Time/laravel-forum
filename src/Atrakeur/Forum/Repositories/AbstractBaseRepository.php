<?php namespace Atrakeur\Forum\Repositories;

abstract class AbstractBaseRepository  {

	protected $model;

	protected $itemsPerPage = 0;

	protected function getFirstBy($index, $value, array $with = array())
	{
		$model = $this->model->where($index, '=', $value)->with($with)->first();
		return $this->model->convertToObject($model);
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

		return $this->model->convertToObject($model);
	}

	public function paginate($itemsPerPage = 0)
	{
		if (!is_numeric($itemsPerPage))
		{
			throw new \InvalidArgumentException();
		}

		$this->itemsPerPage = $itemsPerPage;
	}

	public function getPaginationLinks()
	{
		return $this->model->paginate($this->itemsPerPage)->links();
	}

	public function create(\stdClass $data)
	{
		//TODO validate?
		$array = get_object_vars($data);
		$model = $this->model->create($array);
		return $this->model->convertToObject($model);
	}

	public function update(\stdClass $data)
	{
		//TODO validate?
		$array = get_object_vars($data);
		$model = $this->model->find($array['id']);
		if ($model != null)
		{
			$model->fill($array);
			$model->save();
			return $this->model->convertToObject($model);
		}
		else
		{
			throw new \InvalidArgumentException('Data must contain an existing id to update');
		}
	}

}
