<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\LockThread as Action,
    Events\UserLockedThread,
    Support\Authorization\CategoryAuthorization,
};

class LockThread extends FormRequest implements FulfillableRequestInterface
{
    public function authorize(): bool
    {
        return CategoryAuthorization::lockThreads($this->user(), $this->route('thread')->category);
    }

    public function rules(): array
    {
        return [];
    }

    public function fulfill()
    {
        $action = new Action($this->route('thread'));
        $thread = $action->execute();

        UserLockedThread::dispatch($this->user(), $thread);

        return $thread;
    }
}
