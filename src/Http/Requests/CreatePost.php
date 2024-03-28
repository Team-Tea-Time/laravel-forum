<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\CreatePost as Action,
    Events\UserCreatedPost,
    Support\Authorization\ThreadAuthorization,
    Support\Validation\PostRules,
};

class CreatePost extends FormRequest implements FulfillableRequestInterface
{
    public function authorize(): bool
    {
        return ThreadAuthorization::reply($this->user(), $this->route('thread'));
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
