<?php

namespace TeamTeaTime\Forum\Actions;

use TeamTeaTime\Forum\Models\Category;

class DeleteCategory extends BaseAction
{
    private Category $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    protected function transact()
    {
        return $this->category->delete();
    }
}