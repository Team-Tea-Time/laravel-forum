<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\DeleteCategory as Action,
    Events\UserDeletedCategory,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Support\Authorization\CategoryAuthorization,
    Support\Traits\HandlesDeletion,
    Support\Validation\CategoryRules,
};

class DeleteCategory extends FormRequest implements FulfillableRequestInterface
{
    use AuthorizesAfterValidation, HandlesDeletion;

    public function rules(): array
    {
        return CategoryRules::delete();
    }

    public function withValidator($validator)
    {
        $validator->sometimes('force', 'required', function ($input) {
            return !$this->route('category')->isEmpty();
        });
    }

    public function authorizeValidated(): bool
    {
        return CategoryAuthorization::delete($this->user(), $this->route('category'));
    }

    public function fulfill()
    {
        $category = $this->route('category');

        $action = new Action($category);
        $action->execute();

        UserDeletedCategory::dispatch($this->user(), $category);

        return $category;
    }
}
