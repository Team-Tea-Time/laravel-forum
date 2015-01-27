<?php namespace Riari\Forum\Repositories;

use stdClass;
use Config;

abstract class BaseRepository {

	protected $model;

	protected $itemsPerPage = 0;

	protected function getFirstBy($index, $value, $with = array())
	{
		$model = $this->model->where($index, '=', $value)->with($with)->first();
		return $model;
	}

	public function create($data = array())
	{
		$model = $this->model->create($data);

		return $model;
	}

	public function update($data = array())
	{
		$item = $this->model->find($data['id']);

		$item->fill($data);
		$item->save();

		return $item;
	}

	public function delete($id)
	{
		$item = $this->model->find($id);

		if (Config::get('forum::preferences.soft_delete'))
		{
			$item->delete();
		}
		else
		{
			$item->forceDelete();
		}
	}

}
