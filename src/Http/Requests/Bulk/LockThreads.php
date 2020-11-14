<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Http\Requests\BaseRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Thread;

class LockThreads extends BaseRequest implements FulfillableRequest
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
            if (! $this->user()->can('lockThreads', $category)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        $threads = $this->threads();
        $threads->update(['locked' => true]);

        event(new UserBulkLockedThreads($this->user(), $threads));

        return $threads;
    }

    protected function threads(): Builder
    {
        $query = DB::table(Thread::getTableName());

        if (! $this->user()->can('viewTrashedThreads'))
        {
            $query = $query->whereNull(Thread::DELETED_AT);
        }

        return $query->whereIn('id', $this->validated()['threads']);
    }
}
