<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use TeamTeaTime\Forum\Events\UserViewingCategory;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Support\CategoryAccess;
use TeamTeaTime\Forum\Support\ThreadAccess;

#[Layout('forum::layouts.main')]
class CategoryShow extends Component
{
    use WithPagination;

    public Category $category;
    public ?Category $privateAncestor;
    public Collection $threadDestinationCategories;
    public array $selectableThreadIds = [];

    private LengthAwarePaginator $threads;

    public function mount(Request $request)
    {
        $this->category = $request->route('category');

        if (!$this->category->isAccessibleTo($request->user())) {
            abort(404);
        }

        if ($request->user() !== null) {
            UserViewingCategory::dispatch($request->user(), $this->category);
        }

        $this->privateAncestor = CategoryAccess::getPrivateAncestor($request->user(), $this->category);

        $this->threadDestinationCategories = $request->user() && $request->user()->can('moveCategories')
            ? Category::query()->threadDestinations()->get()
            : [];

        $threads = $request->user() && $request->user()->can('viewTrashedThreads')
            ? $this->category->threads()->withTrashed()
            : $this->category->threads();

        $this->threads = $threads->withPostAndAuthorRelationships()->ordered()->paginate();

        $this->selectableThreadIds = ThreadAccess::getSelectableThreadIdsFor($request->user(), $this->threads, $this->category);
    }

    public function render(): View
    {
        return ViewFactory::make('forum::pages.category.show', ['threads' => $this->threads]);
    }
}
