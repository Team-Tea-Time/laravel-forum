<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Events\UserBulkRestoredThreads;
use TeamTeaTime\Forum\Http\Requests\BaseRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Support\Stats;

class RestoreThreads extends BaseRequest implements FulfillableRequest
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
        if (! $this->user()->can('viewTrashedThreads')) return false;

        $threads = $this->threads()->get();
        foreach ($threads as $thread)
        {
            if (! $this->user()->can('restore', $thread)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        $threads = $this->threads(true)->get();

        if ($threads->count() === 0) return 0;

        $threadsByCategory = $threads->groupBy('category_id');
        foreach ($threadsByCategory as $threads)
        {
            $threadCount = $threads->count();
            $postCount = $threads->sum('reply_count') + $threadCount; // count the first post of each thread
            $category = $threads->first()->category;

            $category->updateWithoutTouch([
                'newest_thread_id' => max($threads->max('id'), $category->newest_thread_id),
                'latest_active_thread_id' => $category->getLatestActiveThreadId(),
                'thread_count' => DB::raw("thread_count + {$threadCount}"),
                'post_count' => DB::raw("post_count + {$postCount}")
            ]);
        }

        event(new UserBulkRestoredThreads($this->user(), $threads));

        return $threads;
    }

    private function threads(bool $withTrashedAbilityCheck = false): Builder
    {
        $query = DB::table(Thread::getTableName());

        if ($withTrashedAbilityCheck && ! $this->user()->can('viewTrashedThreads'))
        {
            $query = $query->whereNull(Thread::DELETED_AT);
        }

        return $query->whereIn('id', $this->validated()['threads']);
    }
}
