<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Actions\DeleteThread as Action;
use TeamTeaTime\Forum\Events\UserDeletedThread;
use TeamTeaTime\Forum\Http\Requests\Traits\HandlesDeletion;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Thread;

class DeleteThread extends FormRequest implements FulfillableRequest
{
    use HandlesDeletion;

    public function authorize(): bool
    {
        $thread = $this->route('thread');
        return $this->user()->can('delete', $thread);
    }

    public function rules(): array
    {
        return [
            'permadelete' => ['boolean']
        ];
    }

    public function fulfill()
    {
        $thread = $this->route('thread');

        $action = new Action($thread, $this->isPermaDeleting());
        $thread = $action->execute();

        if (! is_null($thread))
        {
            event(new UserDeletedThread($this->user(), $thread));
        }

        return $thread;
    }
}
