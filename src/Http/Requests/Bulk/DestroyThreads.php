<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Thread;

class DestroyThreads extends FormRequest implements FulfillableRequest
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
        $thread = $this->posts()->get();
        foreach ($thread as $post)
        {
            if (! $this->user()->can('delete', $thread)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        $threads = $this->threads();

        if (config('forum.general.soft_deletes') && isset($this->validated()['permadelete']) && $this->validated()['permadelete'] && method_exists(Thread::class, 'forceDelete'))
        {
            $threads->forceDelete();
        }
        else
        {
            $threads->delete();
        }

        $threadsByCategory = $threads->select('category_id')->distinct()->get();
        foreach ($threadsByCategory as $thread)
        {
            $thread->category->syncCurrentThreads();
        }

        return $threads->get();
    }

    private function posts(): Builder
    {
        $query = $this->user()->can('viewTrashedPosts') ? Post::withTrashed() : Post::query();
        return $query->whereIn('id', $this->validated()['posts']);
    }
}
