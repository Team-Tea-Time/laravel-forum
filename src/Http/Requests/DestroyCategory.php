<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Category;

class DestroyCategory extends FormRequest implements FulfillableRequest
{
    public function authorize(Category $category): bool
    {
        return $this->user()->can('delete', $category);
    }

    public function fulfill()
    {
        $category = $this->route('category');
        $category->delete();

        return $category;
    }
}
