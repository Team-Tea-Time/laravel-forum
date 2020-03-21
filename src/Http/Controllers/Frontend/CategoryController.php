<?php

namespace TeamTeaTime\Forum\Http\Controllers\Frontend;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use TeamTeaTime\Forum\Events\UserViewingCategory;
use TeamTeaTime\Forum\Events\UserViewingIndex;
use TeamTeaTime\Forum\Http\Requests\UpdateCategory;
use TeamTeaTime\Forum\Http\Requests\StoreCategory;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Support\Frontend\Forum;

class CategoryController extends BaseController
{
    public function index(Request $request): View
    {
        $categories = Category::all()->toTree();

        event(new UserViewingIndex);

        return view('forum::category.index', compact('categories'));
    }

    public function show(Request $request, Category $category): View
    {
        event(new UserViewingCategory($category));

        $categories = Gate::allows('moveCategories') ? Category::topLevel()->get() : [];
        $threads = $category->threadsPaginated;

        return view('forum::category.show', compact('categories', 'category', 'threads'));
    }

    public function store(StoreCategory $request): RedirectResponse
    {
        $category = $request->fulfill();

        Forum::alert('success', 'categories.created');

        return redirect(Forum::route('category.show', $category));
    }

    public function update(UpdateCategory $request): RedirectResponse
    {
        $category = $request->fulfill();

        Forum::alert('success', 'categories.updated', 1);

        return redirect(Forum::route('category.show', $category));
    }

    public function destroy(DestroyCategory $request): RedirectResponse
    {
        $request->fulfill();

        Forum::alert('success', 'categories.deleted', 1);

        return redirect(config('forum.routing.prefix'));
    }

    public function manage(Request $request): View
    {
        $categories = Category::all();
        $categories->makeHidden(['_lft', '_rgt', 'thread_count', 'post_count']);

        return view('forum::category.manage', ['categories' => $categories->toTree()]);
    }
}
