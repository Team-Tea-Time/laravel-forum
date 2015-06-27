<?php namespace Riari\Forum\Repositories;

abstract class BaseRepository {

    protected $model;

    protected $itemsPerPage = 0;

    protected function getFirstBy($index, $value, $with = array())
    {
        $model = $this->model->where($index, '=', $value)->with($with)->first();
        return $model;
    }

    public function getByID($id, $with = array())
    {
        return $this->getFirstBy('id', $id, $with);
    }

    public function create($data = array())
    {
        $model = $this->model->create($data);
        return $model;
    }

    public function update($data = array())
    {
        $model = $this->model->find($data['id'])->update($data);
        return $model;
    }

    public function delete($id)
    {
        $model = $this->model->find($id);

        if (config('forum.preferences.soft_delete'))
        {
            $model->delete();
        }
        else
        {
            $model->forceDelete();
        }

        return $model;
    }

}
