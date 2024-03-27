<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\Bulk\RestorePosts as Action,
    Events\UserBulkRestoredPosts,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Http\Requests\FulfillableRequestInterface,
    Models\Post,
    Support\Validation\PostRules,
};

class RestorePosts extends FormRequest implements FulfillableRequestInterface
{
    use AuthorizesAfterValidation;

    public function rules(): array
    {
        return PostRules::bulk();
    }

    public function authorizeValidated(): bool
    {
        $posts = Post::whereIn('id', $this->validated()['posts'])->onlyTrashed()->get();

        foreach ($posts as $post) {
            if (!($this->user()->can('restorePosts', $post->thread) && $this->user()->can('restore', $post))) {
                return false;
            }
        }

        return true;
    }

    public function fulfill()
    {
        $action = new Action($this->validated()['posts']);
        $posts = $action->execute();

        if ($posts !== null) {
            UserBulkRestoredPosts::dispatch($this->user(), $posts);
        }

        return $posts;
    }
}
