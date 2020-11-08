<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Query\Builder;
use TeamTeaTime\Forum\Http\Requests\BaseRequest;
use TeamTeaTime\Forum\Events\UserBulkPinnedThreads;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class PinThreads extends BaseRequest implements FulfillableRequest
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
        $categoryIds = $this->threads()->select('category_id')->distinct()->pluck('category_id');
        $categories = Category::where('id', $categoryIds)->get();

        foreach ($categories as $category)
        {
            if (! $this->user()->can('pinThreads', $category)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        $this->threads()->update(['pinned' => true]);

        $threads = $this->threads()->get();

        event(new UserBulkPinnedThreads($this->user(), $threads));

        return $threads;
    }

    protected function threads(): Builder
    {
        $query = \DB::table(Thread::getTableName());

        if (! $this->user()->can('viewTrashedThreads'))
        {
            $query = $query->whereNull(Thread::DELETED_AT);
        }

        return $query->whereIn('id', $this->validated()['threads']);
    }
}
