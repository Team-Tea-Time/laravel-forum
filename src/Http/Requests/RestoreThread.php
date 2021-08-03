<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Actions\RestoreThread as Action;
use TeamTeaTime\Forum\Events\UserRestoredThread;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class RestoreThread extends FormRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('restore', $this->route('thread'));
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
