<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\{
    Actions\EditCategory as Action,
    Events\UserEditedCategory,
};

class EditCategory extends CreateCategory
{
    public function fulfill()
    {
        $defaultCategoryColor = config('forum.frontend.default_category_color');

        $input = $this->validated();
        $action = new Action(
            $this->route('category'),
            $input['title'],
            $input['description'] ?? "",
            $input['color_light_mode'] ?? $defaultCategoryColor,
            $input['color_dark_mode'] ?? $defaultCategoryColor,
            $input['accepts_threads'] ?? null,
            $input['is_private'] ?? null,
            $input['thread_approval_enabled'] ?? null,
            $input['post_approval_enabled'] ?? null
        );
        $category = $action->execute();

        UserEditedCategory::dispatch($this->user(), $category);

        return $category;
    }
}
