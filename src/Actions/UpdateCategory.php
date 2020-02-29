<?php

namespace TeamTeaTime\Forum\Actions;

use Illuminate\Database\Eloquent\Collection;
use TeamTeaTime\Forum\Models\Category;

class UpdateCategory
{
    public function execute(int $id, array $attributes): Category
    {
        $category = Category::where('category_id', $id)->first();
        $category->fill($attributes)->save();

        return $category;
    }
}