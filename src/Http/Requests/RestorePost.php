<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\RestorePost as Action,
    Events\UserRestoredPost,
    Support\Authorization\PostAuthorization,
};

class RestorePost extends FormRequest implements FulfillableRequestInterface
{
    public function authorize(): bool
    {
        return PostAuthorization::restore($this->user(), $this->route('post'));
    }

    public function rules(): array
    {
        return [];
    }

    public function fulfill()
    {
        $action = new Action($this->route('post'));
        $post = $action->execute();

        UserRestoredPost::dispatch($this->user(), $post);

        return $post;
    }
}
