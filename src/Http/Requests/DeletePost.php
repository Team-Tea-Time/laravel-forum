<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\DeletePost as Action,
    Events\UserDeletedPost,
    Http\Requests\Traits\HandlesDeletion,
    Support\Validation\PostRules,
};

class DeletePost extends FormRequest implements FulfillableRequestInterface
{
    use HandlesDeletion;

    public function authorize(): bool
    {
        $post = $this->route('post');

        return $post->sequence != 1
            && $this->user()->can('deletePosts', $post->thread)
            && $this->user()->can('delete', $post);
    }

    public function rules(): array
    {
        return PostRules::delete();
    }

    public function fulfill()
    {
        $post = $this->route('post');

        $action = new Action($post, $this->isPermaDeleting());
        $post = $action->execute();

        if ($post !== null) {
            UserDeletedPost::dispatch($this->user(), $post);
        }

        return $post;
    }
}
