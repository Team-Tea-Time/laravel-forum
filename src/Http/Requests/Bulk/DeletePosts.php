<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\Bulk\DeletePosts as Action,
    Events\UserBulkDeletedPosts,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Http\Requests\FulfillableRequestInterface,
    Support\Authorization\PostAuthorization,
    Support\Validation\PostRules,
    Support\Traits\HandlesDeletion,
};

class DeletePosts extends FormRequest implements FulfillableRequestInterface
{
    use AuthorizesAfterValidation, HandlesDeletion;

    public function rules(): array
    {
        return PostRules::bulkDelete();
    }

    public function authorizeValidated(): bool
    {
        return PostAuthorization::bulkDelete($this->user(), $this->validated()['posts']);
    }

    public function fulfill()
    {
        $action = new Action(
            $this->validated()['posts'],
            $this->user()->can('viewTrashedPosts'),
            $this->shouldPermaDelete(isset($this->validated()['permadelete']) && $this->validated()['permadelete'])
        );
        $posts = $action->execute();

        if ($posts !== null) {
            UserBulkDeletedPosts::dispatch($this->user(), $posts);
        }

        return $posts;
    }
}
