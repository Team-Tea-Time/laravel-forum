<?php

namespace TeamTeaTime\Forum\Actions\Bulk;

use TeamTeaTime\Forum\Actions\BaseAction;
use TeamTeaTime\Forum\Models\Category;

class UpdateCategoryTree extends BaseAction
{
    private array $categoryData;

    public function __construct(array $categoryData)
    {
        $this->categoryData = $categoryData;
    }

    protected function transact()
    {
        $saveCount = 0;
        foreach ($this->categoryData as $category) {
            $model = Category::find($category['id']);
            $model->parent_id = isset($category['parent_id'])
                ? $category['parent_id']
                : null;
            if ($model->save()) ++$saveCount;
        }

        return $saveCount;

        // return Category::rebuildTree($this->categoryData);
    }
}
