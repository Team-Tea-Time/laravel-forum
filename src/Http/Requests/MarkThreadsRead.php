<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Thread;

class MarkThreadsRead extends FormRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['int', 'exists:forum_categories,id']
        ];
    }

    public function fulfill()
    {
        $threads = Thread::recent();

        if (isset($this->validated()['category_id']))
        {
            $threads = $threads->where('category_id', $this->validated()['category_id']);
        }

        $threads = $threads->get()->filter(function ($thread)
        {
            return $thread->userReadStatus != null
                && (! $thread->category->private || $this->user()->can('view', $thread->category));
        });

        foreach ($threads as $thread)
        {
            $thread->markAsRead($this->user()->getKey());
        }
    }
}