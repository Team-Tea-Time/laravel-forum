<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Support\Stats;

class RestoreThreads extends FormRequest implements FulfillableRequest
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
        $threads = $this->threads()->get();

        if ($threads->count() === 0) return 0;
        
        // Avoid using Eloquent to prevent automatic touching of updated_at
        $rowsAffected = DB::table((new Thread)->getTable())
            ->whereNotNull('deleted_at')
            ->whereIn('id', array_unique($this->validated()['threads']))
            ->update(['deleted_at' => null]);

        $threadsByCategory = $threads->groupBy('category_id');
        foreach ($threadsByCategory as $categoryId => $threads)
        {
            $threadCount = $threads->count();
            $postCount = $threads->sum('reply_count') + $threadCount; // count the first post of each thread
            $category = $threads->first()->category;

            $category->update([
                'newest_thread_id' => max($threads->max('id'), $category->newest_thread_id),
                'latest_active_thread_id' => $category->getLatestActiveThreadId(),
                'thread_count' => DB::raw("thread_count + {$threadCount}"),
                'post_count' => DB::raw("post_count + {$postCount}")
            ]);
        }

        return $rowsAffected;
    }

    private function threads(): Builder
    {
        return Thread::onlyTrashed()->whereIn('id', array_unique($this->validated()['threads']));
    }
}
