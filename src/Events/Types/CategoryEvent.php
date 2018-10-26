<?php

namespace Riari\Forum\Events\Types;

use Riari\Forum\Models\Category;

class CategoryEvent
{
    /** @var Category */
    public $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }
}
