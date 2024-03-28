<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\Bulk\DeleteThreads as Action,
    Events\UserBulkDeletedThreads,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Http\Requests\Traits\HandlesDeletion,
    Http\Requests\FulfillableRequestInterface,
    Support\Authorization\ThreadAuthorization,
    Support\Validation\ThreadRules,
};

class DeleteThreads extends FormRequest implements FulfillableRequestInterface
{
    use AuthorizesAfterValidation, HandlesDeletion;

    public function rules(): array
    {
        return ThreadRules::bulkDelete();
    }

    public function authorizeValidated(): bool
    {
        return ThreadAuthorization::bulkDelete($this->user(), $this->validated()['threads']);
    }

    public function fulfill()
    {
        $action = new Action(
            $this->validated()['threads'],
            $this->user()->can('viewTrashedPosts'),
            $this->isPermaDeleting()
        );
        $threads = $action->execute();

        if ($threads !== null) {
            UserBulkDeletedThreads::dispatch($this->user(), $threads);
        }

        return $threads;
    }
}
