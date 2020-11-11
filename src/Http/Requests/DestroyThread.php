<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Events\UserDeletedThread;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Thread;

class DestroyThread extends BaseRequest implements FulfillableRequest
{
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

        $thread->readers()->detach();

        if ($this->isPermaDeleting() && method_exists($thread, 'forceDelete'))
        {
            $thread->posts()->withTrashed()->forceDelete();
            $thread->forceDelete();
        }
        else
        {
            $thread->posts()->delete();
            $thread->delete();
        }

        event(new UserDeletedThread($this->user(), $thread));

        $category = $thread->category;

        // Only change category stats and FKs if the thread wasn't already soft-deleted
        if (! $thread->trashed())
        {
            $values = [
                'thread_count' => DB::raw('thread_count - 1'),
                'post_count' => DB::raw("post_count - {$thread->postCount}")
            ];

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

        return $thread;
    }
}
