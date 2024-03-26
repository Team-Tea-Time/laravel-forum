<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\Bulk\LockThreads as Action,
    Events\UserBulkLockedThreads,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Interfaces\FulfillableRequest,
    Models\Category,
    Models\Thread,
    Support\CategoryAccess,
    Support\Validation\ThreadRules,
};

class LockThreads extends FormRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation;

    public function rules(): array
    {
        return ThreadRules::bulk();
    }

    public function authorizeValidated(): bool
    {
        $query = Thread::whereIn('id', $this->validated()['threads']);

        if ($this->user()->can('viewTrashedThreads')) {
            $query = $query->withTrashed();
        }

        $categoryIds = $query->select('category_id')->distinct()->pluck('category_id');
        $categories = Category::whereIn('id', $categoryIds)->get();

        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($this->user());

        foreach ($categories as $category) {
            if (! ($accessibleCategoryIds->contains($category->id) || $this->user()->can('lockThreads', $category))) {
                return false;
            }
        }

        return true;
    }

    public function fulfill()
    {
        $action = new Action($this->validated()['threads'], $this->user()->can('viewTrashedThreads'));
        $threads = $action->execute();

        if ($threads !== null) {
            UserBulkLockedThreads::dispatch($this->user(), $threads);
        }

        return $threads;
    }
}
