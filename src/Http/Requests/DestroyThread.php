<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class DestroyThread extends FormRequest implements FulfillableRequest
{
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

        if (config('forum.general.soft_deletes') && isset($this->validated()['permadelete']) && $this->validated()['permadelete'] && method_exists($thread, 'forceDelete'))
        {
            $thread->forceDelete();
        }
        else
        {
            $thread->delete();
        }

        $thread->category->syncCurrentThreads();

        return $thread;
    }
}
