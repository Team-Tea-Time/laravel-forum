<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\Bulk\DeletePosts as Action,
    Events\UserBulkDeletedPosts,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Http\Requests\Traits\HandlesDeletion,
    Interfaces\FulfillableRequest,
    Models\Post,
    Support\CategoryAccess,
    Support\Validation\PostRules,
};

class DeletePosts extends FormRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation, HandlesDeletion;

    public function rules(): array
    {
        return PostRules::bulkDelete();
    }

    public function authorizeValidated(): bool
    {
        $query = Post::query();

        if ($this->user()->can('viewTrashedPosts')) {
            $query = $query->withTrashed();
        }

        $posts = $query->with(['thread', 'thread.category'])->whereIn('id', $this->validated()['posts']);

        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($this->user());

        foreach ($posts as $post) {
            $canView = $accessibleCategoryIds->contains($post->thread->category_id) && $this->user()->can('view', $post->thread);
            $canDelete = $this->user()->can('deletePosts', $post->thread) && $this->user()->can('delete', $post);

            if (! ($canView && $canDelete)) {
                return false;
            }
        }

        return true;
    }

    public function fulfill()
    {
        $action = new Action(
            $this->validated()['posts'],
            $this->user()->can('viewTrashedPosts'),
            $this->isPermaDeleting()
        );
        $posts = $action->execute();

        if ($posts !== null) {
            UserBulkDeletedPosts::dispatch($this->user(), $posts);
        }

        return $posts;
    }
}
