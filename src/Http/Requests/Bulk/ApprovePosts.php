<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\Bulk\ApprovePosts as Action,
    Events\UserBulkApprovedPosts,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Http\Requests\FulfillableRequestInterface,
    Support\Authorization\PostAuthorization,
    Support\Validation\PostRules,
};

class ApprovePosts extends FormRequest implements FulfillableRequestInterface
{
    use AuthorizesAfterValidation;

    public function rules(): array
    {
        return PostRules::bulk();
    }

    public function authorizeValidated(): bool
    {
        return PostAuthorization::bulkApprove($this->user(), $this->validated()['posts']);
    }

    public function fulfill()
    {
        $action = new Action($this->validated()['posts']);
        $posts = $action->execute();

        if ($posts !== null) {
            UserBulkApprovedPosts::dispatch($this->user(), $posts);
        }

        return $posts;
    }
}
