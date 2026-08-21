<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Actions\Bulk\ApprovePosts,
    Actions\Bulk\DeletePosts,
    Events\UserBulkApprovedPosts,
    Events\UserBulkDeletedPosts,
    Events\UserBulkUnapprovedThreads,
    Events\UserViewingPostsPendingApproval,
    Http\Livewire\Traits\CreatesAlerts,
    Http\Livewire\Traits\HandlesBulkActions,
    Http\Livewire\Traits\UpdatesContent,
    Models\Post,
    Support\Access\CategoryAccess,
    Support\Authorization\PostAuthorization,
};

class PostsPendingApproval extends Component
{
    use CreatesAlerts, HandlesBulkActions, UpdatesContent;

    protected Collection $threads;

    protected function getPosts(Request $request): Collection
    {
        $posts = Post::notDeleted()
            ->notFirstInThread()
            ->pendingApproval()
            ->orderBy('created_at', 'desc')
            ->with('thread.category', 'author', 'parent', 'parent.author', 'parent.thread.category');

        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($request->user());

        return $posts->get()->filter(function ($post) use ($request, $accessibleCategoryIds) {
            return !$post->thread->category->is_private || $request->user() && $accessibleCategoryIds->contains($post->category_id) && $request->user()->can('view', $post->thread);
        });
    }

    public function mount(Request $request)
    {
        if (!$request->user()->can('approvePosts')) {
            abort(404);
        }

        $this->touchUpdateKey();
    }

    public function approve(Request $request, array $postIds)
    {
        if (!PostAuthorization::bulkApprove($request->user(), $postIds)) {
            abort(403);
        }

        $action = new ApprovePosts($postIds);
        $result = $action->execute();

        if ($result !== null) {
            UserBulkApprovedPosts::dispatch($request->user(), $result);
        }

        return $this->handleActionResult($result, 'posts.approved');
    }

    public function delete(Request $request, array $postIds)
    {
        if (!PostAuthorization::bulkDelete($request->user(), $postIds)) {
            abort(403);
        }

        $action = new DeletePosts($postIds, false);
        $result = $action->execute();

        if ($result !== null) {
            UserBulkDeletedPosts::dispatch($request->user(), $result);
        }

        return $this->handleActionResult($result, 'posts.deleted');
    }

    public function render(Request $request): View
    {
        $posts = $this->getPosts($request);

        UserViewingPostsPendingApproval::dispatch($request->user(), $posts);

        return ViewFactory::make('forum::pages.post.pending-approval', [
            'posts' => $posts,
        ])->layout('forum::layouts.main');
    }
}
