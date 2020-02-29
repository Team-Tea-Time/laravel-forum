<?php

namespace TeamTeaTime\Forum\Actions;

use Illuminate\Database\Eloquent\Collection;
use TeamTeaTime\Forum\Models\Category;

class DeleteCategory
{
    public function execute(int $destroy): int
    {
        return Category::destroy($id);
    }
}