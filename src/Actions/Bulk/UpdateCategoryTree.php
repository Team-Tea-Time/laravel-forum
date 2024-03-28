<?php

namespace TeamTeaTime\Forum\Actions\Bulk;

use TeamTeaTime\Forum\Actions\BaseAction;
use TeamTeaTime\Forum\Models\Category;

class UpdateCategoryTree extends BaseAction
{
    private array $tree;

    public function __construct(array $tree)
    {
        $this->tree = $tree;
    }

    protected function transact()
    {
        return Category::rebuildTree($this->tree);
    }
}
