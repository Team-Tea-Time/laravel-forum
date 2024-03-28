<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\Bulk\UpdateCategoryTree as Action,
    Events\UserBulkManagedCategories,
    Http\Requests\FulfillableRequestInterface,
    Support\Authorization\CategoryAuthorization,
    Support\Validation\CategoryRules,
};

class ManageCategories extends FormRequest implements FulfillableRequestInterface
{
    public function rules(): array
    {
        return CategoryRules::bulk();
    }

    public function authorizeValidated(): bool
    {
        return CategoryAuthorization::manage($this->user());
    }

    public function fulfill()
    {
        $categoryData = $this->validated()['categories'];
        $action = new Action($categoryData);
        $categoriesAffected = $action->execute();

        UserBulkManagedCategories::dispatch($this->user(), $categoriesAffected, $categoryData);

        return $categoriesAffected;
    }
}
