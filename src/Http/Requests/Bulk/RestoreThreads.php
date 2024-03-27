<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\Bulk\RestoreThreads as Action,
    Events\UserBulkRestoredThreads,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Http\Requests\FulfillableRequestInterface,
    Models\Thread,
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
        if (!$this->user()->can('viewTrashedThreads')) {
            return false;
        }

        $threads = Thread::whereIn('id', $this->validated()['threads'])->get();
        foreach ($threads as $thread) {
            if (!($this->user()->can('restoreThreads', $thread->category) && $this->user()->can('restore', $thread))) {
                return false;
            }
        }

        return true;
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
