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
}