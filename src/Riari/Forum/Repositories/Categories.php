<?php namespace Riari\Forum\Repositories;

use Riari\Forum\Models\Category;

class Categories extends BaseRepository {

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->where('parent_category', '=', null)->orderBy('weight', 'DESC')->get();
    }

}
