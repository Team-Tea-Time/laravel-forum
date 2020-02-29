<?php

namespace TeamTeaTime\Forum\Actions;

use Illuminate\Database\Eloquent\Collection;
use TeamTeaTime\Forum\Models\Category;

class CreateCategory
{
    public function execute(array $attributes): Category
    {
        return Category::create($attributes);
    }
}