<?php

namespace TeamTeaTime\Forum\Actions;

use Illuminate\Database\Eloquent\Collection;
use TeamTeaTime\Forum\Models\Category;

class GetTopLevelCategories
{
    public function execute(): Collection
    {
        return Category::where('parent_id', 0)->get();
    }
}