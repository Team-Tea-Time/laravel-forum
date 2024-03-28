<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\MarkThreadsAsRead as Action,
    Events\UserMarkedThreadsAsRead,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Models\Category,
    Support\Authorization\CategoryAuthorization,
    Support\Validation\CategoryRules,
};

class MarkThreadsAsRead extends FormRequest implements FulfillableRequestInterface
{
    use AuthorizesAfterValidation;

    private ?Category $category;

    public function rules(): array
    {
        return CategoryRules::markThreadsAsRead();
    }

    public function authorizeValidated(): bool
    {
        return CategoryAuthorization::markThreadsAsRead($this->user(), $this->getCategory());
    }

    public function fulfill()
    {
        $category = $this->getCategory();

        $action = new Action($this->user(), $category);
        $threads = $action->execute();

        UserMarkedThreadsAsRead::dispatch($this->user(), $category, $threads);

        return $category;
    }

    private function getCategory()
    {
        if (!isset($this->category)) {
            $this->category = isset($this->validated()['category_id']) ? Category::find($this->validated()['category_id']) : null;
        }

        return $this->category;
    }
}
