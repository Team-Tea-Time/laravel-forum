<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Events\UserBulkMovedThreads;
use TeamTeaTime\Forum\Http\Requests\BaseRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\BaseModel;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class MoveThreads extends BaseRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation;

    private Category $destinationCategory;
    private Collection $threadsByCategory;

    public function rules(): array
    {
        return [
            'threads' => ['required', 'array'],
            'category_id' => ['required', 'int', 'exists:forum_categories,id']
        ];
    }

    public function authorizeValidated(): bool
    {
        if (! $this->user()->can('moveThreadsTo', $this->getDestinationCategory())) return false;

        foreach ($this->threadsByCategory as $thread)
        {
            if (! $this->user()->can('moveThreadsFrom', $thread->category)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        foreach ($this->getThreadsByCategory() as $thread)
        {
            $thread->category->syncCurrentThreads();
        }

        $this->threads()->update(['category_id' => $this->validated()['category_id']]);

        $threads = $this->threads()->get();

        event(new UserMovedThreads($this->user(), $threads, $this->getDestinationCategory));
        
        $this->getDestinationCategory()->syncCurrentThreads();

        return $threads;
    }

    private function threads(): Builder
    {
        $query = DB::table(Thread::getTableName());

        if (! $this->user()->can('viewTrashedThreads'))
        {
            $query = $query->whereNull(Category::DELETED_AT);
        }

        return $query->whereIn('id', $this->validated()['threads']);
    }

    private function getThreadsByCategory()
    {
        if (! $this->threadsByCategory)
        {
            $this->threadsByCategory = $this->threads()->select('category_id')->distinct()->get();
        }
        
        return $this->threadsByCategory;
    }

    private function getDestinationCategory()
    {
        if (! $this->destinationCategory)
        {
            $this->destinationCategory = Category::find($this->validated()['category_id']);
        }
        
        return $this->destinationCategory;
    }
}
