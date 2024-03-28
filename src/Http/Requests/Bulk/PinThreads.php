<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\Bulk\PinThreads as Action,
    Events\UserBulkPinnedThreads,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Http\Requests\FulfillableRequestInterface,
    Support\Authorization\ThreadAuthorization,
    Support\Validation\ThreadRules,
};

class PinThreads extends FormRequest implements FulfillableRequestInterface
{
    use AuthorizesAfterValidation;

    public function rules(): array
    {
        return ThreadRules::bulk();
    }

    public function authorizeValidated(): bool
    {
        return ThreadAuthorization::bulkPin($this->user(), $this->validated()['threads']);
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
