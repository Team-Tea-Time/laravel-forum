<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\Actions\UpdateCategory as Action;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Events\UserUpdatedCategory;

class UpdateCategory extends CreateCategory
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

        if (! is_null($category)) {
            event(new UserUpdatedCategory($this->user(), $category));
        }

        return $category;
    }
}
