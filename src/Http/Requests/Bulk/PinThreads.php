<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;

class PinThreads extends FormRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation;

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
            if (! $this->user()->can('pinThreads', $thread->category)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        return $this->threads()->update(['pinned' => true]);
    }

    private function threads(): Builder
    {
        $query = \DB::table(with(Thread::class)->getTable());
        $query = $this->user()->can('viewTrashedThreads') ? $query->withTrashed() : $query;
        return $query->whereIn('id', $this->validated()['threads']);
    }
}
