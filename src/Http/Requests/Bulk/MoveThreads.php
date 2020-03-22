<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Category;

class MoveThreads extends FormRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation;

    /** @var Category */
    private $targetCategory;

    /** @var Collection */
    private $threadsByCategory;

    public function rules(): array
    {
        return [
            'threads' => ['required', 'array'],
            'category_id' => ['required', 'int', 'exists:forum_categories,id']
        ];
    }

    public function authorizeValidated(): bool
    {
        if (! $this->user()->can('moveThreadsTo', $this->getTargetCategory())) return false;

        foreach ($this->threadsByCategory as $thread)
        {
            if (! $this->user()->can('moveThreadsFrom', $thread->category)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        foreach ($this->threadsByCategory as $thread)
        {
            $thread->category->syncCurrentThreads();
        }

        $this->threads()->update(['category_id' => $this->validated()['category_id']]);
        
        $this->targetCategory->syncCurrentThreads();
    }

    private function threads(): Builder
    {
        $query = \DB::table(with(Thread::class)->getTable());
        $query = $this->user()->can('viewTrashedThreads') ? $query->withTrashed() : $query;

        return $query->whereIn('id', $this->validated()['threads']);
    }

    private function getThreadsByCategory()
    {
        if (isset($this->threadsByCategory)) return $this->threadsByCategory;

        $this->threadsByCategory = $this->threads()->select('category_id')->distinct()->get();
    }

    private function getTargetCategory()
    {
        if (isset($this->targetCategory)) return $this->targetCategory;

        $this->targetCategory = Category::find($this->validated()['category_id']);
    }
}
