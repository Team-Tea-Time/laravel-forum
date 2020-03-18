<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class StoreThread extends FormRequest implements FulfillableRequest
{
    public function authorize(Category $category): bool
    {
        return $this->user()->can('createThreads', $category);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:' . config('forum.general.validation.title_min')],
            'content' => ['required', 'string', 'min:' . config('forum.general.validation.content_min')]
        ];
    }

    public function fulfill()
    {
        $input = $this->validated();

        $thread = Thread::create([
            'author_id' => $this->user()->getKey(),
            'category_id' => $this->route('category')->id,
            'title' => $input['title']
        ]);

        $thread->posts()->create([
            'author_id' => $this->user()->getKey(),
            'content' => $input['content']
        ]);

        return $thread;
    }
}
