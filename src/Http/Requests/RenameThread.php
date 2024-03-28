<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\RenameThread as Action,
    Events\UserRenamedThread,
    Support\Authorization\ThreadAuthorization,
    Support\Validation\ThreadRules,
};

class RenameThread extends FormRequest implements FulfillableRequestInterface
{
    public function authorize(): bool
    {
        return ThreadAuthorization::rename($this->user(), $this->route('thread'));
    }

    public function rules(): array
    {
        return ThreadRules::rename();
    }

    public function fulfill()
    {
        $action = new Action($this->route('thread'), $this->validated()['title']);
        $thread = $action->execute();

        UserRenamedThread::dispatch($this->user(), $thread);

        return $thread;
    }
}
