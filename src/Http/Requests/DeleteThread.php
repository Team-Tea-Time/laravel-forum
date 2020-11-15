<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Events\UserDeletedThread;
use TeamTeaTime\Forum\Http\Requests\Traits\HandlesDeletion;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Thread;

class DeleteThread extends FormRequest implements FulfillableRequest
{
    use HandlesDeletion;

    public function authorize(): bool
    {
        $thread = $this->route('thread');
        return $this->user()->can('delete', $thread);
    }

    public function rules(): array
    {
        return [
            'permadelete' => ['boolean']
        ];
    }

    public function fulfill()
    {
        $thread = $this->route('thread');

        $threadAlreadyTrashed = $thread->trashed();
        $postsRemoved = $thread->postCount;

        if ($this->isPermaDeleting())
        {
            $thread->readers()->detach();
            $thread->posts()->withTrashed()->forceDelete();
            $thread->forceDelete();
        }
        else
        {
            // Return early if the thread was already trashed because there's nothing to do
            if ($threadAlreadyTrashed) return $thread;

            $thread->readers()->detach();
            $thread->posts()->delete();
            $thread->deleteWithoutTouch();
        }

        // Only update category stats and FKs if the thread wasn't already soft-deleted,
        // otherwise they'll needlessly be updated a second time
        if (! $threadAlreadyTrashed)
        {
            $values = [
                'thread_count' => DB::raw('thread_count - 1'),
                'post_count' => DB::raw("post_count - {$postsRemoved}")
            ];

            $category = $thread->category;

            if ($category->newest_thread_id === $thread->id)
            {
                $values['newest_thread_id'] = $category->getNewestThreadId();
            }
            if ($category->latest_active_thread_id === $thread->id)
            {
                $values['latest_active_thread_id'] = $category->getLatestActiveThreadId();
            }

            $category->update($values);
        }

        event(new UserDeletedThread($this->user(), $thread));

        return $thread;
    }
}
