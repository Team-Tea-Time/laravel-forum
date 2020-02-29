<?php

namespace TeamTeaTime\Forum\Events\Types;

use TeamTeaTime\Forum\Models\Category;

class CategoryEvent
{
    /** @var Category */
    public $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }
}
