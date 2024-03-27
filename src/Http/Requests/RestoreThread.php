<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\RestoreThread as Action,
    Events\UserRestoredThread,
};

class RestoreThread extends FormRequest implements FulfillableRequestInterface
{
    public function authorize(): bool
    {
        $thread = $this->route('thread');

        return $this->user()->can('restoreThreads', $thread->category) && $this->user()->can('restore', $thread);
    }

    public function rules(): array
    {
        return [];
    }

    public function fulfill()
    {
        $action = new Action($this->route('thread'));
        $thread = $action->execute();

        if (! $thread === null) {
            UserRestoredThread::dispatch($this->user(), $thread);
        }

        return $thread;
    }
}
