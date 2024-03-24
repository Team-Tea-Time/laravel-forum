<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\Actions\CreatePost as Action;
use TeamTeaTime\Forum\Events\UserCreatedPost;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Support\Validation\PostRules;

class CreatePost extends FormRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reply', $this->route('thread'));
    }

    public function rules(): array
    {
        return PostRules::create();
    }

    public function fulfill()
    {
        $thread = $this->route('thread');
        $parent = $this->has('post') ? $thread->posts->find($this->input('post')) : null;

        $action = new Action($thread, $parent, $this->user(), $this->validated()['content']);
        $post = $action->execute();

        UserCreatedPost::dispatch($this->user(), $post);

        return $post;
    }
}
