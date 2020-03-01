<?php

namespace TeamTeaTime\Forum\Http\Requests;

class UpdateCategory extends CreateCategory
{
    public function fulfill()
    {
        $category = $this->route('category');
        $category->fill($this->validated())->save();

        return $category;
    }
}
