<?php

namespace TeamTeaTime\Forum\Actions\Bulk;

use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Actions\BaseAction;
use TeamTeaTime\Forum\Models\Category;

class ManageCategories extends BaseAction
{
    private array $categoryData;

    public function __construct(array $categoryData)
    {
        $this->categoryData = $categoryData;
    }

    protected function transact()
    {
        return Category::rebuildTree($this->categoryData);
    }
}