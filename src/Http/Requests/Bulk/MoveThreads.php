<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Category;

class MoveThreads extends FormRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation;

    public function rules(): array
    {
        return [
            'threads' => ['required', 'array'],
            'category_id' => ['required', 'int', 'exists:forum_categories,id']
        ];
    }

    public function authorizeValidated(): bool
    {
        $targetCategory = Category::find($this->validated()['category_id']);

        if (! $this->user()->can('moveThreadsTo', $targetCategory)) return false;

        $threads = $this->threads()->select('category_id')->distinct()->get();

        foreach ($threads as $thread)
        {
            if (! $this->user()->can('moveThreadsFrom', $thread->category)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        return $this->threads()->update(['category_id' => $this->validated()['category_id']]);
    }

    private function threads(): Builder
    {
        $query = \DB::table(with(Thread::class)->getTable());
        $query = $this->user()->can('viewTrashedThreads') ? $query->withTrashed() : $query;
        return $query->whereIn('id', $this->validated()['threads']);
    }
}
