<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\DeleteThread as Action,
    Events\UserDeletedThread,
    Http\Requests\Traits\HandlesDeletion,
    Support\Authorization\ThreadAuthorization,
    Support\Validation\ThreadRules,
};

class DeleteThread extends FormRequest implements FulfillableRequestInterface
{
    use HandlesDeletion;

    public function authorize(): bool
    {
        return ThreadAuthorization::delete($this->user(), $this->route('thread'));
    }

    public function rules(): array
    {
        return ThreadRules::delete();
    }

    public function fulfill()
    {
        $action = new Action($this->route('thread'), $this->isPermaDeleting());
        $thread = $action->execute();

        if (! $thread === null) {
            UserDeletedThread::dispatch($this->user(), $thread);
        }

        return $thread;
    }
}
