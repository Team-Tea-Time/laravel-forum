<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\ApproveThread as Action,
    Events\UserApprovedThread,
    Support\Authorization\ThreadAuthorization,
};

class ApproveThread extends FormRequest implements FulfillableRequestInterface
{
    public function authorize(): bool
    {
        return ThreadAuthorization::approve($this->user(), $this->route('thread'));
    }

    public function rules(): array
    {
        return [];
    }

    public function fulfill()
    {
        $action = new Action($this->route('thread'));
        $thread = $action->execute();

        if ($thread !== null) {
            UserApprovedThread::dispatch($this->user(), $thread);
        }

        return $thread;
    }
}
