<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\RestoreThread as Action,
    Events\UserRestoredThread,
    Support\Authorization\ThreadAuthorization,
};

class RestoreThread extends FormRequest implements FulfillableRequestInterface
{
    public function authorize(): bool
    {
        return ThreadAuthorization::restore($this->user(), $this->route('thread'));
    }

    public function rules(): array
    {
        return [];
    }

    public function fulfill()
    {
        $action = new Action($this->route('thread'));
        $thread = $action->execute();

        UserRestoredThread::dispatch($this->user(), $thread);

        return $thread;
    }
}
