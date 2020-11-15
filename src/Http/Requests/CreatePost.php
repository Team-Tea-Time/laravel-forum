<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Events\UserCreatedPost;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;

class CreatePost extends FormRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reply', $this->route('thread'));
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
            'author_id' => $this->user()->getKey(),
            'sequence' => $thread->posts->count() + 1
        ]);

        event(new UserCreatedPost($this->user(), $post));

        $thread->update([
            'last_post_id' => $post->id,
            'reply_count' => DB::raw('reply_count + 1')
        ]);

        $thread->category->updateWithoutTouch([
            'latest_active_thread_id' => $thread->id,
            'post_count' => DB::raw('post_count + 1')
        ]);

        return $post;
    }
}
