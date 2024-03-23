<?php

namespace TeamTeaTime\Forum\Http\Controllers\Blade;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use TeamTeaTime\Forum\Events\UserViewingCategory;
use TeamTeaTime\Forum\Events\UserViewingIndex;
use TeamTeaTime\Forum\Http\Requests\CreateCategory;
use TeamTeaTime\Forum\Http\Requests\DeleteCategory;
use TeamTeaTime\Forum\Http\Requests\UpdateCategory;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Support\CategoryAccess;
use TeamTeaTime\Forum\Support\ThreadAccess;
use TeamTeaTime\Forum\Support\Frontend\Forum;

class CategoryController extends BaseController
{
    public function index(Request $request): View
    {
        $categories = CategoryAccess::getFilteredTreeFor($request->user());

        if ($request->user() !== null) {
            UserViewingIndex::dispatch($request->user());
        }

        return ViewFactory::make('forum.category.index', compact('categories'));
    }

    public function show(Request $request): View
    {
        $category = $request->route('category');

        if (! $category->isAccessibleTo($request->user())) {
            abort(404);
        }

        if ($request->user() !== null) {
            UserViewingCategory::dispatch($request->user(), $category);
        }

        $privateAncestor = CategoryAccess::getPrivateAncestor($request->user(), $category);

        $threadDestinationCategories = $request->user() && $request->user()->can('moveCategories')
            ? Category::query()->threadDestinations()->get()
            : [];

        $threads = $request->user() && $request->user()->can('viewTrashedThreads')
            ? $category->threads()->withTrashed()
            : $category->threads();

        $threads = $threads->withPostAndAuthorRelationships()->ordered()->paginate();

        $selectableThreadIds = ThreadAccess::getSelectableThreadIdsFor($request->user(), $threads, $category);

        return ViewFactory::make('forum.category.show', compact('privateAncestor', 'threadDestinationCategories', 'category', 'threads', 'selectableThreadIds'));
    }

    public function store(CreateCategory $request): RedirectResponse
    {
        $category = $request->fulfill();

        Forum::alert('success', 'categories.created');

        return new RedirectResponse(Forum::route('category.show', $category));
    }

    public function update(UpdateCategory $request): RedirectResponse
    {
        $category = $request->fulfill();

        if ($category === null) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'categories.updated', 1);

        return new RedirectResponse(Forum::route('category.show', $category));
    }

    public function delete(DeleteCategory $request): RedirectResponse
    {
        $request->fulfill();

        Forum::alert('success', 'categories.deleted', 1);

        return new RedirectResponse(Forum::route('index'));
    }

    public function manage(Request $request): View
    {
        $categories = Category::defaultOrder()->get();
        $categories->makeHidden(['_lft', '_rgt', 'thread_count', 'post_count']);

        return ViewFactory::make('forum.category.manage', ['categories' => $categories->toTree()]);
    }
}
