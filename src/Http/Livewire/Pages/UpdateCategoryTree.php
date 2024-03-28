<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Actions\Bulk\UpdateCategoryTree as Action,
    Events\UserBulkManagedCategories,
    Events\UserManagingCategories,
    Http\Livewire\Traits\CreatesAlerts,
    Models\Category,
    Support\Authorization\CategoryAuthorization,
};

class UpdateCategoryTree extends Component
{
    use CreatesAlerts;

    public array $tree = [];

    public function mount(Request $request)
    {
        if (!CategoryAuthorization::move($request->user())) {
            abort(404);
        }

        if ($request->user() !== null) {
            UserManagingCategories::dispatch($request->user());
        }
    }

    public function save(Request $request): array
    {
        if (!CategoryAuthorization::move($request->user())) {
            abort(403);
        }

        $action = new Action($this->tree);
        $result = $action->execute();

        UserBulkManagedCategories::dispatch($request->user(), $result, $this->tree);

        return $this->alert('general.changes_applied')->toLivewire();
    }

    public function render(): View
    {
        $categories = Category::defaultOrder()->get();
        $categories->makeHidden(['_lft', '_rgt', 'thread_count', 'post_count']);

        return ViewFactory::make('forum::pages.category.manage', [
            'categories' => $categories->toTree(),
        ])->layout('forum::layouts.main');
    }
}
