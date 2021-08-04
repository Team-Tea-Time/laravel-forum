<?php

namespace TeamTeaTime\Forum\Http\Controllers\Web;

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
use TeamTeaTime\Forum\Support\Web\Forum;

class CategoryController extends BaseController
{
    public function index(Request $request): View
    {
        $categories = Category::defaultOrder()
            ->with('newestThread', 'latestActiveThread', 'newestThread.lastPost', 'latestActiveThread.lastPost')
            ->get()
            ->filter(function ($category) use ($request) {
                return ! $category->is_private || $request->user() && $request->user()->can('view', $category);
            })
            ->toTree();

        if ($request->user() !== null) {
            UserViewingIndex::dispatch($request->user());
        }

        return ViewFactory::make('forum::category.index', compact('categories'));
    }

    public function show(Request $request, Category $category): View
    {
        if ($category->is_private) {
            $this->authorize('view', $category);
        }

        if ($request->user() !== null) {
            UserViewingCategory::dispatch($request->user(), $category);
        }

        $categories = $request->user() && $request->user()->can('moveCategories')
            ? Category::defaultOrder()
                ->with('children')
                ->withDepth()
                ->get()
            : [];

        $threads = $request->user() && $request->user()->can('viewTrashedThreads')
            ? $category->threads()->withTrashed()
            : $category->threads();

        $threads = $threads
            ->with('firstPost', 'lastPost', 'firstPost.author', 'lastPost.author', 'author')
            ->orderBy('pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate();

        return ViewFactory::make('forum::category.show', compact('categories', 'category', 'threads'));
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

        return ViewFactory::make('forum::category.manage', ['categories' => $categories->toTree()]);
    }
}
