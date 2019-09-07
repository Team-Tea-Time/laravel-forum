<?php

namespace Riari\Forum\Services;

use Riari\Forum\Models\Category;

class CategoryService
{
    /** @var Model */
    private $model;

    public function __construct()
    {
        $this->model = new Category;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getTopLevel()
    {
        return $this->model->where('parent_id', 0)->get();
    }

    public function getByID(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create($attributes)
    {
        return $this->model->create($attributes);
    }
}