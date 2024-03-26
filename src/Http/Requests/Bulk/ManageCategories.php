<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\Bulk\ManageCategories as Action,
    Events\UserBulkManagedCategories,
    Interfaces\FulfillableRequest,
    Support\Validation\CategoryRules,
};

class ManageCategories extends FormRequest implements FulfillableRequest
{
    public function rules(): array
    {
        return CategoryRules::bulk();
    }

    public function authorizeValidated(): bool
    {
        return $this->user()->can('manageCategories');
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
