<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Events\UserCreatedCategory;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Category;

class StoreCategory extends FormRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('createCategories');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:' . config('forum.general.validation.title_min')],
            'description' => ['nullable', 'string'],
            'accepts_threads' => ['boolean'],
            'is_private' => ['boolean'],
            'color' => ['string']
        ];
    }

    public function fulfill()
    {
        $category = Category::create($this->validated());

        event(new UserCreatedCategory($this->user(), $category));

        return $category;
    }
}
