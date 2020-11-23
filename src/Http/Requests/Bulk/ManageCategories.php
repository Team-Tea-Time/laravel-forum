<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Actions\Bulk\ManageCategories as Action;
use TeamTeaTime\Forum\Events\UserBulkManagedCategories;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Category;

class ManageCategories extends FormRequest implements FulfillableRequest
{
    public function rules(): array
    {
        return [
            'categories' => ['required', 'array']
        ];
    }

    public function authorizeValidated(): bool
    {
        return $this->user()->can('manageCategories');
    }

    public function fulfill()
    {
        $action = new Action($this->validated()['categories']);
        $categories = $action->execute();

        event(new UserBulkManagedCategories($this->user(), $categories));

        return $categories;
    }
}
