<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use TeamTeaTime\Forum\{
    Actions\Bulk\DeleteThreads,
    Actions\Bulk\LockThreads,
    Actions\Bulk\PinThreads,
    Actions\Bulk\RestoreThreads,
    Actions\Bulk\UnlockThreads,
    Actions\Bulk\UnpinThreads,
    Events\UserBulkDeletedThreads,
    Events\UserBulkLockedThreads,
    Events\UserBulkPinnedThreads,
    Events\UserBulkRestoredThreads,
    Events\UserBulkUnlockedThreads,
    Events\UserBulkUnpinnedThreads,
    Events\UserViewingCategory,
    Http\Livewire\Traits\CreatesAlerts,
    Http\Livewire\Traits\UpdatesContent,
    Http\Livewire\EventfulPaginatedComponent,
    Models\Category,
    Support\Authorization\ThreadAuthorization,
    Support\Access\CategoryAccess,
    Support\Access\ThreadAccess,
    Support\Traits\HandlesDeletion,
};

class CategoryShow extends EventfulPaginatedComponent
{
    use CreatesAlerts, UpdatesContent, HandlesDeletion;

    public Category $category;

    public function mount(Request $request)
    {
        $this->category = $request->route('category');
        $this->touchUpdateKey();

        if (!$request->route('category')->isAccessibleTo($request->user())) {
            abort(404);
        }

        if ($request->user() !== null) {
            UserViewingCategory::dispatch($request->user(), $this->category);
        }
    }

    private function handleActionResult($result, string $key = 'threads.updated'): array
    {
        if ($result == null) {
            return $this->invalidSelectionAlert()->toLivewire();
        }

        $this->touchUpdateKey();

        return $this->pluralAlert($key, $result->count())->toLivewire();
    }

    public function deleteThreads(Request $request, array $threadIds, bool $permadelete): array
    {
        if (!ThreadAuthorization::bulkDelete($request->user(), $threadIds)) {
            abort(403);
        }

        $action = new DeleteThreads(
            $threadIds,
            $request->user()->can('viewTrashedPosts'),
            $this->shouldPermaDelete($permadelete));
        $result = $action->execute();

        if ($result !== null) {
            UserBulkDeletedThreads::dispatch($request->user(), $result);
        }

        return $this->handleActionResult($result, 'threads.deleted');
    }

    public function restoreThreads(Request $request, array $threadIds): array
    {
        if (!ThreadAuthorization::bulkRestore($request->user(), $threadIds)) {
            abort(403);
        }

        $action = new RestoreThreads($threadIds);
        $result = $action->execute();

        if ($result !== null) {
            UserBulkRestoredThreads::dispatch($request->user(), $result);
        }

        return $this->handleActionResult($result, 'threads.restored');
    }

    public function lockThreads(Request $request, array $threadIds): array
    {
        if (!ThreadAuthorization::bulkLock($request->user(), $threadIds)) {
            abort(403);
        }

        $action = new LockThreads($threadIds, $request->user()->can('viewTrashedThreads'));
        $result = $action->execute();

        if ($result !== null) {
            UserBulkLockedThreads::dispatch($request->user(), $result);
        }

        return $this->handleActionResult($result);
    }

    public function unlockThreads(Request $request, array $threadIds): array
    {
        if (!ThreadAuthorization::bulkLock($request->user(), $threadIds)) {
            abort(403);
        }

        $action = new UnlockThreads($threadIds, $request->user()->can('viewTrashedThreads'));
        $result = $action->execute();

        if ($result !== null) {
            UserBulkUnlockedThreads::dispatch($request->user(), $result);
        }

        return $this->handleActionResult($result);
    }

    public function pinThreads(Request $request, array $threadIds): array
    {
        if (!ThreadAuthorization::bulkPin($request->user(), $threadIds)) {
            abort(403);
        }

        $action = new PinThreads($threadIds, $request->user()->can('viewTrashedThreads'));
        $result = $action->execute();

        if ($result !== null) {
            UserBulkPinnedThreads::dispatch($request->user(), $result);
        }

        return $this->handleActionResult($result);
    }

    public function unpinThreads(Request $request, array $threadIds): array
    {
        if (!ThreadAuthorization::bulkPin($request->user(), $threadIds)) {
            abort(403);
        }

        $action = new UnpinThreads($threadIds, $request->user()->can('viewTrashedThreads'));
        $result = $action->execute();

        if ($result !== null) {
            UserBulkUnpinnedThreads::dispatch($request->user(), $result);
        }

        return $this->handleActionResult($result);
    }

    private function getThreads(Request $request): LengthAwarePaginator
    {
        $threads = $request->user() && $request->user()->can('viewTrashedThreads')
            ? $this->category->threads()->withTrashed()
            : $this->category->threads();

        return $threads->withPostAndAuthorRelationships()->ordered()->paginate();
    }

    public function render(Request $request): View
    {
        $user = $request->user();
        $threads = $this->getThreads($request);
        $privateAncestor = CategoryAccess::getPrivateAncestor($user, $this->category);
        $selectableThreadIds = ThreadAccess::getSelectableThreadIdsFor(
            $user,
            $threads,
            $this->category);

        return ViewFactory::make('forum::pages.category.show', [
            'category' => $this->category,
            'threads' => $threads,
            'privateAncestor' => $privateAncestor,
            'selectableThreadIds' => $selectableThreadIds,
        ])->layout('forum::layouts.main', ['category' => $this->category]);
    }
}
