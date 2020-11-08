<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Events\UserMarkedThreadsAsRead;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class MarkThreadsAsRead extends FormRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation;

    private Category $category;

    public function rules(): array
    {
        return [
            'category_id' => ['int', 'exists:forum_categories,id']
        ];
    }

    public function authorizeValidated(): bool
    {
        $category = $this->category();

        if ($category !== null && ! $this->user()->can('view', $category)) return false;

        return $this->user()->can('markThreadsAsRead', $category);
    }

    public function fulfill()
    {
        $threads = Thread::recent();
        $category = $this->category();

        if ($category !== null)
        {
            $threads = $threads->where('category_id', $category->id);
        }

        $threads = $threads->get()->filter(function ($thread)
        {
            return $thread->userReadStatus != null
                && (! $thread->category->private || $this->user()->can('view', $thread->category));
        });

        foreach ($threads as $thread)
        {
            $thread->markAsRead($this->user()->getKey());
        }

        event(new UserMarkedThreadsAsRead($this->user(), $category, $threads));

        return $category;
    }

    private function category()
    {
        if (! isset($this->category))
        {
            $this->category = isset($this->validated()['category_id']) ? Category::find($this->validated()['category_id']) : null;
        }

        return $this->category;
    }
}