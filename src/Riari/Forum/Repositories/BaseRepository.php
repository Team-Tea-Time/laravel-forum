<?php namespace Riari\Forum\Repositories;

use Illuminate\Database\Eloquent\Model;
use Riari\Forum\Contracts\Repository;

abstract class BaseRepository implements Repository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var int
     */
    protected $perPage;

    /**
     * Create a new repository instance.
     *
     * @param  Model  $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->perPage = 20;
    }

    /**
     * Get all rows.
     *
     * @param  array  $columns
     * @return  Collection
     */
    public function all($columns = ['*'])
    {
        return $this->model->get($columns);
    }

    /**
     * Get paginated rows.
     *
     * @param  array  $columns
     * @return LengthAwarePaginator
     */
    public function paginate($columns = ['*'])
    {
        return $this->model->paginate($this->perPage, $columns);
    }

    /**
     * Create a row with the given data.
     *
     * @param  array  $data
     * @return Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a row with the given ID and data.
     *
     * @param  int  $id
     * @param  array  $data
     * @return Model
     */
    public function update($id, array $data)
    {
        $model = $this->find($id);

        if (!is_null($model)) {
            $model->fill($data);
            $model->save();
        }

        return $model;
    }

    /**
     * Delete a row with the given ID.
     *
     * @param  int  $id
     * @return Model
     */
    public function delete($id = 0)
    {
        $model = $this->find($id);

        if (!is_null($model)) {
            if (!config('forum.preferences.misc.soft_delete')) {
                return $model->forceDelete();
            }

            return $model->delete();
        }

        return $model;
    }

    /**
     * Fetch a row with the given ID.
     *
     * @param  int  $id
     * @param  array  $columns
     * @return Model
     */
    public function find($id = 0, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Fetch rows matching the specified column/value.
     *
     * @param  string  $column
     * @param  mixed  $value
     * @return Collection
     */
    public function findBy($column = '', $value, $columns = ['*'])
    {
        return $this->model->where($column, $value)->paginate($this->perPage, $columns);
    }
}
