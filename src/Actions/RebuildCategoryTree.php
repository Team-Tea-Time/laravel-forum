<?php

namespace TeamTeaTime\Forum\Actions;

use Illuminate\Database\Eloquent\Collection;
use TeamTeaTime\Forum\Models\Category;

class RebuildCategoryTree
{
    public function execute(array $categories): int
    {
        return Category::rebuildTree($categories);
    }
}