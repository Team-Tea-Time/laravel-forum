<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\DeletePost as Action,
    Events\UserDeletedPost,
    Support\Authorization\PostAuthorization,
    Support\Validation\PostRules,
    Support\Traits\HandlesDeletion,
};

class DeletePost extends FormRequest implements FulfillableRequestInterface
{
    use HandlesDeletion;

    public function authorize(): bool
    {
        return PostAuthorization::delete($this->user(), $this->route('post'));
    }

    public function rules(): array
    {
        return PostRules::delete();
    }

    public function fulfill()
    {
        $post = $this->route('post');

        $action = new Action($post, $this->shouldPermaDelete(isset($this->validated()['permadelete']) && $this->validated()['permadelete']));
        $post = $action->execute();

        if ($post !== null) {
            UserDeletedPost::dispatch($this->user(), $post);
        }

        return $post;
    }
}
