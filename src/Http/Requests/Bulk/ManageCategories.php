<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
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
        return Category::rebuildTree($this->validated()['categories']);
    }
}
