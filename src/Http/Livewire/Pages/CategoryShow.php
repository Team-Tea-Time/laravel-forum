<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Actions\Bulk\DeleteThreads,
    Actions\Bulk\LockThreads,
    Actions\Bulk\PinThreads,
    Actions\Bulk\RestoreThreads,
    Actions\Bulk\MoveThreads,
    Actions\Bulk\UnlockThreads,
    Actions\Bulk\UnpinThreads,
    Events\UserBulkDeletedThreads,
    Events\UserBulkLockedThreads,
    Events\UserBulkMovedThreads,
    Events\UserBulkPinnedThreads,
    Events\UserBulkRestoredThreads,
    Events\UserBulkUnlockedThreads,
    Events\UserBulkUnpinnedThreads,
    Events\UserViewingCategory,
    Http\Livewire\Traits\CreatesAlerts,
    Http\Livewire\Traits\UpdatesContent,
    Models\BaseModel,
    Models\Category,
    Models\Thread,
    Support\Access\CategoryAccess,
    Support\Access\ThreadAccess,
    Support\Authorization\ThreadAuthorization,
    Support\Traits\HandlesDeletion,
};

class CategoryShow extends Component
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

    public function moveThreads(Request $request, array $threadIds, int $destinationCategoryId): array
    {
        $destination = Category::find($destinationCategoryId);

        $query = Thread::select('category_id')
            ->distinct()
            ->where('category_id', '!=', $destination->id)
            ->whereIn('id', $threadIds);

        if (!$request->user()->can('viewTrashedThreads')) {
            $query = $query->whereNull(BaseModel::DELETED_AT);
        }

        $sourceCategories = Category::whereIn('id', $query->get()->pluck('category_id'))->get();

        if (!ThreadAuthorization::bulkMove($request->user(), $sourceCategories, $destination)) {
            abort(403);
        }

        $action = new MoveThreads($threadIds, $destination, $request->user()->can('viewTrashedThreads'));
        $result = $action->execute();

        if ($result !== null) {
            UserBulkMovedThreads::dispatch($request->user(), $result);
        }

        return $this->handleActionResult($result);
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

        if ($this->category->requiresThreadApproval() && !$request->user() || !$request->user()->can('approveThreads', $this->category)) {
            $threads = $threads->authoredByOrApproved($request->user());
        }

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
        $threadDestinationCategories = $request->user() && $request->user()->can('moveCategories')
            ? Category::query()->threadDestinations()->get()
            : [];

        return ViewFactory::make('forum::pages.category.show', [
            'category' => $this->category,
            'threads' => $threads,
            'privateAncestor' => $privateAncestor,
            'selectableThreadIds' => $selectableThreadIds,
            'threadDestinationCategories' => $threadDestinationCategories,
        ])->layout('forum::layouts.main', ['category' => $this->category]);
    }
}
