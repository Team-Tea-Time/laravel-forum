<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Events\UserBulkDeletedThreads;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Http\Requests\Traits\HandlesDeletion;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Thread;

class DeleteThreads extends FormRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation, HandlesDeletion;

    public function rules(): array
    {
        return [
            'threads' => ['required', 'array'],
            'permadelete' => ['boolean']
        ];
    }

    public function authorizeValidated(): bool
    {
        // Eloquent is used here so that we get a collection of Thread instead of
        // stdClass in order for the gate to infer the policy to use.
        $threads = Thread::whereIn('id', $this->validated()['threads'])->get();
        foreach ($threads as $thread)
        {
            if (! $this->user()->can('delete', $thread)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        $query = DB::table(Thread::getTableName())->whereIn('id', $this->validated()['threads']);

        $threads = $this->isPermaDeleting() ? $query->get() : $query->whereNull(Thread::DELETED_AT)->get();

        if ($threads->count() === 0) return 0;

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
}
