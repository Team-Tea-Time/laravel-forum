<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Actions\Bulk\ApproveThreads,
    Actions\Bulk\DeleteThreads,
    Events\UserBulkApprovedThreads,
    Events\UserBulkDeletedThreads,
    Events\UserMarkedThreadsAsRead,
    Events\UserViewingThreadsPendingApproval,
    Http\Livewire\Traits\CreatesAlerts,
    Http\Livewire\Traits\HandlesBulkActions,
    Http\Livewire\Traits\UpdatesContent,
    Models\Thread,
    Support\Access\CategoryAccess,
    Support\Access\ThreadAccess,
    Support\Authorization\ThreadAuthorization,
};

class ThreadsPendingApproval extends Component
{
    use CreatesAlerts, HandlesBulkActions, UpdatesContent;

    protected Collection $threads;

    protected function getThreads(Request $request): Collection
    {
        $threads = Thread::notDeleted()->pendingApproval()->orderBy('created_at', 'desc')->with('category', 'author', 'lastPost', 'lastPost.author', 'lastPost.thread');

        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($request->user());

        return $threads->get()->filter(function ($thread) use ($request, $accessibleCategoryIds) {
            return !$thread->category->is_private || $request->user() && $accessibleCategoryIds->contains($thread->category_id) && $request->user()->can('view', $thread);
        });
    }

    public function mount(Request $request)
    {
        if (!$request->user()->can('approveThreads')) {
            abort(404);
        }

        $this->touchUpdateKey();
    }

    public function approve(Request $request, array $threadIds)
    {
        if (!ThreadAuthorization::bulkApprove($request->user(), $threadIds)) {
            abort(403);
        }

        $action = new ApproveThreads($threadIds);
        $result = $action->execute();

        if ($result !== null) {
            UserBulkApprovedThreads::dispatch($request->user(), $result);
        }

        return $this->handleActionResult($result, 'threads.approved');
    }

    public function delete(Request $request, array $threadIds)
    {
        // TODO: implement me
        if (!ThreadAuthorization::bulkDelete($request->user(), $threadIds)) {
            abort(403);
        }

        $action = new DeleteThreads($threadIds, false);
        $result = $action->execute();

        if ($result !== null) {
            UserBulkDeletedThreads::dispatch($request->user(), $result);
        }

        return $this->handleActionResult($result, 'threads.deleted');
    }

    public function render(Request $request): View
    {
        $user = $request->user();
        $threads = $this->getThreads($request);

        UserViewingThreadsPendingApproval::dispatch($request->user(), $threads);

        return ViewFactory::make('forum::pages.thread.pending-approval', [
            'threads' => $threads,
        ])->layout('forum::layouts.main');
    }
}
