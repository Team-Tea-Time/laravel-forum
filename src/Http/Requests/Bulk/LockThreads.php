<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Http\Requests\Traits\BulkQueriesThreads;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class LockThreads extends FormRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation, BulkQueriesThreads;

    public function rules(): array
    {
        return [
            'threads' => ['required', 'array']
        ];
    }

    public function authorizeValidated(): bool
    {
        $threads = $this->threads()->select('category_id')->distinct()->get();
        foreach ($threads as $thread)
        {
            if (! $this->user()->can('lockThreads', $thread->category)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        return $this->threads()->update(['locked' => true]);
    }
}
