<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Events\UserUpdatedCategory;
use TeamTeaTime\Forum\Models\Category;

class UpdateCategory extends CreateCategory
{
    public function fulfill()
    {
        $category = $this->route('category');
        $input = $this->validated() + ['accepts_threads' => 0, 'is_private' => 0]; // Defaults for checkbox inputs
        $category->fill($input)->save();

        event(new UserUpdatedCategory($this->user(), $category));

        return $category;
    }
}
