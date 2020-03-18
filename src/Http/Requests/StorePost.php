<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;

class StorePost extends FormRequest implements FulfillableRequest
{
    public function authorize(Thread $thread): bool
    {
        return $this->user()->can('reply', $thread);
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:' . config('forum.general.validation.content_min')]
        ];
    }

    public function fulfill()
    {
        $thread = $this->route('thread');

        $parent = $this->has('post') ? $thread->posts->find($this->input('post'))->id : 0;

        $post = Post::create($this->validated() + [
            'thread_id' => $thread->id,
            'post_id' => $parent,
            'author_id' => $this->user()->getKey()
        ]);

        $post->thread->touch();

        return $post;
    }
}
