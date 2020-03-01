<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Category;

class CreateCategory extends FormRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('createCategories');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5'],
            'description' => ['nullable', 'string'],
            'accepts_threads' => ['boolean'],
            'is_private' => ['boolean'],
            'color' => ['string']
        ];
    }

    public function fulfill()
    {
        return Category::create($this->validated());
    }
}
