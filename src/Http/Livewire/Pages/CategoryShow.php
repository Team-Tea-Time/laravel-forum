<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use TeamTeaTime\Forum\Actions\Bulk\LockThreads;
use TeamTeaTime\Forum\Actions\Bulk\UnlockThreads;
use TeamTeaTime\Forum\Events\UserViewingCategory;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Support\CategoryAccess;
use TeamTeaTime\Forum\Support\ThreadAccess;
use TeamTeaTime\Forum\Http\Livewire\EventfulPaginatedComponent;

class CategoryShow extends EventfulPaginatedComponent
{
    public Category $category;

    /**
     * A unique string used to trigger updates when threads are updated.
     */
    public string $updateKey;

    private function touchUpdateKey()
    {
        $this->updateKey = uniqid();
    }

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

    public function lockThreads(Request $request, array $threadIds): array
    {
        $action = new LockThreads($threadIds, false);
        $result = $action->execute();

        if ($result == null) {
            return [
                'type' => 'warning',
                'message' => trans('forum::general.invalid_selection'),
            ];
        }

        $this->touchUpdateKey();

        return [
            'type' => 'success',
            'message' => trans_choice("forum::threads.updated", $result->count())
        ];
    }

    public function unlockThreads(Request $request, array $threadIds): array
    {
        $action = new UnlockThreads($threadIds, false);
        $result = $action->execute();

        if ($result == null) {
            return [
                'type' => 'warning',
                'message' => trans('forum::general.invalid_selection'),
            ];
        }

        $this->touchUpdateKey();

        return [
            'type' => 'success',
            'message' => trans_choice("forum::threads.updated", $result->count())
        ];
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
        $threads = $this->getThreads($request);
        $privateAncestor = CategoryAccess::getPrivateAncestor($request->user(), $this->category);
        $selectableThreadIds = ThreadAccess::getSelectableThreadIdsFor(
            $request->user(),
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
