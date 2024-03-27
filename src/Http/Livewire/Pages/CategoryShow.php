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
    Events\UserViewingCategory,
    Http\Livewire\Traits\CreatesAlerts,
    Http\Livewire\Traits\UpdatesContent,
    Http\Livewire\EventfulPaginatedComponent,
    Models\Category,
    Support\CategoryAccess,
    Support\ThreadAccess,
};

class CategoryShow extends EventfulPaginatedComponent
{
    use CreatesAlerts, UpdatesContent;

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
        $action = new DeleteThreads(
            $threadIds,
            $request->user()->can('viewTrashedPosts'),
            $permadelete);

        $result = $action->execute();
        return $this->handleActionResult($result, 'threads.deleted');
    }

    public function restoreThreads(Request $request, array $threadIds): array
    {
        $action = new RestoreThreads($threadIds);
        $result = $action->execute();
        return $this->handleActionResult($result, 'threads.restored');
    }

    public function lockThreads(Request $request, array $threadIds): array
    {
        $action = new LockThreads($threadIds, false);
        $result = $action->execute();
        return $this->handleActionResult($result);
    }

    public function unlockThreads(Request $request, array $threadIds): array
    {
        $action = new UnlockThreads($threadIds, false);
        $result = $action->execute();
        return $this->handleActionResult($result);
    }

    public function pinThreads(Request $request, array $threadIds): array
    {
        $action = new PinThreads($threadIds, false);
        $result = $action->execute();
        return $this->handleActionResult($result);
    }

    public function unpinThreads(Request $request, array $threadIds): array
    {
        $action = new UnpinThreads($threadIds, false);
        $result = $action->execute();
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

        $bulkActions = [];
        if ($user->can('deleteThreads', $this->category)) {
            $bulkActions['delete'] = trans('forum::general.delete');
        }
        if ($user->can('restoreThreads', $this->category)) {
            $bulkActions['restore'] = trans('forum::general.restore');
        }
        if ($user->can('moveThreadsFrom', $this->category)) {
            $bulkActions['move'] = trans('forum::general.move');
        }
        if ($user->can('lockThreads', $this->category)) {
            $bulkActions['lock'] = trans('forum::threads.lock');
            $bulkActions['unlock'] = trans('forum::threads.unlock');
        }
        if ($user->can('pinThreads', $this->category)) {
            $bulkActions['pin'] = trans('forum::threads.pin');
            $bulkActions['unpin'] = trans('forum::threads.unpin');
        }

        return ViewFactory::make('forum::pages.category.show', [
            'category' => $this->category,
            'threads' => $threads,
            'privateAncestor' => $privateAncestor,
            'selectableThreadIds' => $selectableThreadIds,
            'bulkActions' => $bulkActions,
        ])->layout('forum::layouts.main', ['category' => $this->category]);
    }
}
