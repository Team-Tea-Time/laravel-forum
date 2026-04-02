<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Actions\Bulk\UpdateCategoryTree as Action,
    Events\UserBulkReorderedCategories,
    Events\UserReorderingCategories,
    Http\Livewire\Traits\CreatesAlerts,
    Models\Category,
    Support\Access\CategoryAccess,
    Support\Authorization\CategoryAuthorization,
};

class UpdateCategoryTree extends Component
{
    use CreatesAlerts;

    public array $tree = [];

    public function mount(Request $request)
    {
        if (!$request->user() || !CategoryAuthorization::move($request->user())) {
            abort(404);
        }

        UserReorderingCategories::dispatch($request->user());
    }

    public function save(Request $request): array
    {
        if (!CategoryAuthorization::move($request->user())) {
            abort(403);
        }

        $action = new Action($this->tree);
        $result = $action->execute();

        UserBulkReorderedCategories::dispatch($request->user(), $result, $this->tree);

        return $this->alert('general.changes_applied')->toLivewire();
    }

    public function render(): View
    {
        $categories = Category::defaultOrder()->get();
        $categories->makeHidden(['_lft', '_rgt', 'thread_count', 'post_count']);

        return ViewFactory::make('forum::pages.category.manage', [
            'categories' => CategoryAccess::removeParentRelationships($categories->toTree()),
        ])->layout('forum::layouts.main');
    }
}
