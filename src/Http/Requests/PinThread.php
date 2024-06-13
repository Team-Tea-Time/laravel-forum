<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\PinThread as Action,
    Events\UserPinnedThread,
    Support\Authorization\CategoryAuthorization,
};

class PinThread extends FormRequest implements FulfillableRequestInterface
{
    public function authorize(): bool
    {
        return CategoryAuthorization::pinThreads($this->user(), $this->route('thread')->category);
    }

    public function rules(): array
    {
        return [];
    }

    public function fulfill()
    {
        $action = new Action($this->route('thread'));
        $thread = $action->execute();

        UserPinnedThread::dispatch($this->user(), $thread);

        return $thread;
    }
}
