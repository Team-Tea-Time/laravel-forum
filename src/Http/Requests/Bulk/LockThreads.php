<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\Bulk\LockThreads as Action,
    Events\UserBulkLockedThreads,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Http\Requests\FulfillableRequestInterface,
    Support\Authorization\ThreadAuthorization,
    Support\Validation\ThreadRules,
};

class LockThreads extends FormRequest implements FulfillableRequestInterface
{
    use AuthorizesAfterValidation;

    public function rules(): array
    {
        return ThreadRules::bulk();
    }

    public function authorizeValidated(): bool
    {
        return ThreadAuthorization::bulkLock($this->user(), $this->validated()['threads']);
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
