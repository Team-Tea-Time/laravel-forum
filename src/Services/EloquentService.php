<?php

namespace Riari\Forum\Services;

use Illuminate\Database\Eloquent\Model;

abstract class EloquentService
{
    /** @var Model */
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}