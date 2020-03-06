<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Category;

class UpdateCategory extends FormRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('createCategories');
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'string', 'in:rename,enable-threads,make-private']
            'title' => ['required_if:action,rename', 'string', 'min:5'],
            'description' => ['nullable', 'string'],
            'accepts_threads' => ['boolean'],
            'is_private' => ['boolean'],
            'color' => ['string']
        ];
    }

    public function fulfill()
    {
        $category = $this->route('category');
        $input = $this->validated();


        $category->fill($this->validated())->save();

        return $category;
    }
}
