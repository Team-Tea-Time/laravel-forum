<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class DestroyThread extends FormRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        $thread = $this->route('thread');
        return $this->user()->can('deleteThreads', $thread->category);
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

        if (! config('forum.general.soft_deletes') || $this->input('permadelete') && method_exists($thread, 'forceDelete'))
        {
            $thread->forceDelete();
        }
        else
        {
            $thread->delete();
        }

        return $thread;
    }
}
