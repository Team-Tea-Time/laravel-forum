<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\CreateCategory as Action,
    Events\UserCreatedCategory,
    Support\Authorization\CategoryAuthorization,
    Support\Validation\CategoryRules,
};

class CreateCategory extends FormRequest implements FulfillableRequestInterface
{
    public function authorize(): bool
    {
        return CategoryAuthorization::create($this->user());
    }

    public function rules(): array
    {
        return CategoryRules::create();
    }

    public function fulfill()
    {
        $input = $this->validated();

        $action = new Action(
            $input['title'],
            isset($input['description']) ? $input['description'] : '',
            isset($input['color_light_mode']) ? $input['color_light_mode'] : config('forum.frontend.default_category_color'),
            isset($input['color_dark_mode']) ? $input['color_dark_mode'] : config('forum.frontend.default_category_color'),
            isset($input['accepts_threads']) && $input['accepts_threads'],
            isset($input['is_private']) && $input['is_private']
        );

        $category = $action->execute();

        UserCreatedCategory::dispatch($this->user(), $category);

        return $category;
    }
}
