<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\Bulk\PinThreads as Action,
    Events\UserBulkPinnedThreads,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Interfaces\FulfillableRequest,
    Support\CategoryAccess,
    Support\Validation\ThreadRules,
};

class PinThreads extends FormRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation;

    public function rules(): array
    {
        return ThreadRules::bulk();
    }

    public function authorizeValidated(): bool
    {
        $categories = CategoryAccess::getFilteredCategoryCollectionFor($this->user(), $this->validated()['threads']);

        foreach ($categories as $category) {
            if (!$this->user()->can('pinThreads', $category)) {
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
            UserBulkPinnedThreads::dispatch($this->user(), $threads);
        }

        return $threads;
    }
}
