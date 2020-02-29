<?php

namespace TeamTeaTime\Forum\Actions;

use Illuminate\Database\Eloquent\Collection;
use TeamTeaTime\Forum\Models\Category;

class GetTopLevelCategories
{
    public function execute(int $id): Category
    {
        return Category::where('category_id', $id)->first();
    }
}