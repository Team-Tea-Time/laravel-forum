<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Thread;

class DestroyThread extends FormRequest implements FulfillableRequest
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
        
        $threadIsTrashed = $thread->trashed();

        if ($this->isPermaDeleting() && method_exists($thread, 'forceDelete'))
        {
            $thread->posts()->withTrashed()->forceDelete();
            $thread->forceDelete();
        }
        else
        {
            $thread->delete();
        }

        $category = $thread->category;

        if (! $threadIsTrashed)
        {
            // Only change category stats and FKs if the thread wasn't already soft-deleted
            $category->update([
                'newest_thread_id' => $category->getNewestThreadId(),
                'latest_active_thread_id' => $category->getLatestActiveThreadId(),
                'thread_count' => DB::raw('thread_count - 1'),
                'post_count' => DB::raw("post_count - {$thread->postCount}")
            ]);
        }

        return $thread;
    }

    private function isPermaDeleting(): bool
    {
        return ! config('forum.general.soft_deletes') || isset($this->validated()['permadelete']) && $this->validated()['permadelete'];
    }
}
