<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\Bulk\RestoreThreads as Action,
    Events\UserBulkRestoredThreads,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Http\Requests\FulfillableRequestInterface,
    Support\Authorization\ThreadAuthorization,
    Support\Validation\ThreadRules,
};

class RestoreThreads extends FormRequest implements FulfillableRequestInterface
{
    use AuthorizesAfterValidation;

    public function rules(): array
    {
        return ThreadRules::bulk();
    }

    public function authorizeValidated(): bool
    {
        return ThreadAuthorization::bulkRestore($this->user(), $this->validated()['threads']);
    }

    public function fulfill()
    {
        $action = new Action($this->validated()['threads']);
        $threads = $action->execute();

        if ($threads !== null) {
            UserBulkRestoredThreads::dispatch($this->user(), $threads);
        }

        return $threads;
    }
}
