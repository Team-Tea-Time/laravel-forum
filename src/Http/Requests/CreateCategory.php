<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Actions\CreateCategory as Action;
use TeamTeaTime\Forum\Events\UserCreatedCategory;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class CreateCategory extends FormRequest implements FulfillableRequest
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
            'color' => ['string'],
            'accepts_threads' => ['boolean'],
            'is_private' => ['boolean']
        ];
    }

    public function fulfill()
    {
        $input = $this->validated();

        $action = new Action(
            $input['title'],
            $input['description'],
            $input['color'],
            $input['accepts_threads'],
            $input['is_private']
        );

        $category = $action->execute();

        event(new UserCreatedCategory($this->user(), $category));

        return $category;
    }
}
