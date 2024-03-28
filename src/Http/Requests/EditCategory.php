<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\{
    Actions\UpdateCategory as Action,
    Events\UserEditedCategory,
};

class EditCategory extends CreateCategory
{
    public function fulfill()
    {
        $input = $this->validated();
        $action = new Action(
            $this->route('category'),
            $input['title'] ?? null,
            $input['description'] ?? null,
            $input['color'] ?? null,
            $input['accepts_threads'] ?? null,
            $input['is_private'] ?? null
        );
        $category = $action->execute();

        if (!$category === null) {
            UserEditedCategory::dispatch($this->user(), $category);
        }

        return $category;
    }
}
