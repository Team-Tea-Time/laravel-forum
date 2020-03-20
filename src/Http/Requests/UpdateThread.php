<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Thread;

class UpdateThread extends FormRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        $thread = $this->route('thread');

        if (! $this->user()->can('manageThreads', $thread->category)) return false;

        $action = $this->input('action');
        switch ($action)
        {
            case 'lock':
            case 'unlock':
                return $this->user()->can('lockThreads', $thread->category);
            case 'pin':
            case 'unpin':
                return $this->user()->can('pinThreads', $thread->category);
            case 'rename':
                return $this->user()->can('rename', $thread);
            case 'move':
                return $this->user()->can('moveThreadsFrom', $thread->category);
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'string', 'in:lock,unlock,pin,unpin,rename,moveThreadsFrom'],
            'title' => ['required_if:action,rename', 'string', 'min:' . config('forum.general.validation.title_min')],
            'category_id' => ['required_if:action,move', 'int', 'exists:forum_categories,id']
        ];
    }

    public function fulfill()
    {
        $thread = $this->route('thread');
        $action = $this->input('action');
        
        switch ($action)
        {
            case 'lock':
                $thread->locked = true;
                break;
            case 'unlock':
                $thread->locked = false;
                break;
            case 'pin':
                $thread->pinned = true;
                break;
            case 'unpin':
                $thread->pinned = false;
                break;
            case 'rename':
                $thread->title = $this->input('title');
                break;
            case 'move':
                $thread->category_id = $this->input('category_id');
                break;
        }

        $thread->save();

        return $thread;
    }
}