<?php

namespace TeamTeaTime\Forum\Events\Types;

use TeamTeaTime\Forum\Models\Category;

class CategoryEvent
{
    /** @var mixed */
    public $user;

    /** @var Category */
    public $category;

    public function __construct($user, Category $category)
    {
        $this->user = $user;
        $this->category = $category;
    }
}
