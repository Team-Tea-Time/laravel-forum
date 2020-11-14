<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Events\UserBulkDeletedThreads;
use TeamTeaTime\Forum\Http\Requests\BaseRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Thread;

class DestroyThreads extends BaseRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation;

    public function rules(): array
    {
        return [
            'threads' => ['required', 'array'],
            'permadelete' => ['boolean']
        ];
    }

    public function authorizeValidated(): bool
    {
        $threads = $this->threads()->get();
        foreach ($threads as $thread)
        {
            if (! $this->user()->can('delete', $thread)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        $threads = $this->isPermaDeleting() ? $this->threads()->get() : $this->threads()->whereNull(Thread::DELETED_AT)->get();

        if ($threads->count() === 0) return 0;
        
        // Avoid using Eloquent to prevent automatic touching of updated_at
        $query = $this->threads();
        $rowsAffected = $this->isPermaDeleting()
            ? $query->delete()
            : $query->whereNull(Thread::DELETED_AT)->update([Thread::DELETED_AT => DB::raw('NOW()')]);

        $threadsByCategory = $threads->groupBy('category_id');
        foreach ($threadsByCategory as $threads)
        {
            // Count only non-deleted threads for changes to category stats since soft-deleted threads
            // are already represented
            $threadCount = $threads->whereNull(Thread::DELETED_AT)->count();

            // Sum of reply counts + thread count = total posts
            $postCount = $threads->whereNull(Thread::DELETED_AT)->sum('reply_count') + $threadCount;

            $category = $threads->first()->category;

            $updates = [
                'newest_thread_id' => $category->getNewestThreadId(),
                'latest_active_thread_id' => $category->getLatestActiveThreadId()
            ];

            if ($threadCount > 0) $updates['thread_count'] = DB::raw("thread_count - {$threadCount}");
            if ($postCount > 0) $updates['post_count'] = DB::raw("post_count - {$postCount}");

            $category->update($updates);
        }

        event(new UserBulkDeletedThreads($this->user(), $threads));

        return $rowsAffected;
    }

    private function threads(): Builder
    {
        return DB::table(Thread::getTableName())->whereIn('id', $this->validated()['threads']);
    }
}
