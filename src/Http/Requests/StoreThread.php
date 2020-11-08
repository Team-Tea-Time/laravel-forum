<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\Events\UserStoredThread;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class StoreThread extends BaseRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        $category = $this->route('category');
        return $category->accepts_threads && $this->user()->can('createThreads', $category);
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
        $category = $this->route('category');

        $thread = Thread::create([
            'author_id' => $this->user()->getKey(),
            'category_id' => $category->id,
            'title' => $input['title']
        ]);

        $post = $thread->posts()->create([
            'author_id' => $this->user()->getKey(),
            'content' => $input['content']
        ]);

        event(new UserStoredThread($this->user(), $thread));

        $thread->update([
            'first_post_id' => $post->id,
            'last_post_id' => $post->id
        ]);

        $thread->category->update([
            'newest_thread_id' => $thread->id,
            'latest_active_thread_id' => $thread->id
        ]);

        return $thread;
    }
}
