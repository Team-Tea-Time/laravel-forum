<?php

namespace TeamTeaTime\Forum\Actions;

use Illuminate\Database\Eloquent\Collection;
use TeamTeaTime\Forum\Models\Category;

class GetAllCategories
{
    public function execute(): Collection
    {
        return Category::all();
    }
}