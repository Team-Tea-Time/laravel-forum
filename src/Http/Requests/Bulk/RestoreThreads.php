<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Thread;

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
        $thread = $this->posts()->get();
        foreach ($thread as $post)
        {
            if (! $this->user()->can('restore', $thread)) return false;
        }

        return true;
    }

    public function fulfill()
    {
        $threads = $this->threads();
        $threads->restore();

        return $threads->get();
    }

    private function posts(): Builder
    {
        $query = $this->user()->can('viewTrashedPosts') ? Post::withTrashed() : Post::query();
        return $query->whereIn('id', $this->validated()['posts']);
    }
}
