<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Events\UserDeletedCategory;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\HandlesDeletion;
use TeamTeaTime\Forum\Models\Category;

class DeleteCategory extends FormRequest implements FulfillableRequest
{
    use HandlesDeletion;

    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('category'));
    }

    public function fulfill()
    {
        $category = $this->route('category');
        $category->delete();

        event(new UserDeletedCategory($this->user(), $category));

        return $category;
    }
}
