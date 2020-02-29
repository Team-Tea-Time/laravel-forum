<?php namespace TeamTeaTime\Forum\Http\Controllers\Frontend;

use Forum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use TeamTeaTime\Forum\Actions\CreateCategory;
use TeamTeaTime\Forum\Actions\GetAllCategories;
use TeamTeaTime\Forum\Actions\GetCategoryById;
use TeamTeaTime\Forum\Actions\GetCategoryTree;
use TeamTeaTime\Forum\Actions\UpdateCategory;
use TeamTeaTime\Forum\Events\UserViewingCategory;
use TeamTeaTime\Forum\Events\UserViewingIndex;
use TeamTeaTime\Forum\Http\Requests\StoreCategory;

class CategoryController extends BaseController
{
    public function index(Request $request, GetAllCategories $action): View
    {
        $categories = $action->execute()->toTree();

        event(new UserViewingIndex);

        return view('forum::category.index', compact('categories'));
    }

    public function show(Request $request, GetCategoryById $getCategoryById, GetTopLevelCategories $getTopLevelCategories): View
    {
        $category = $getCategoryById->execute($request->route('category'));

        event(new UserViewingCategory($category));

        $categories = Gate::allows('moveCategories') ? $getTopLevelCategories->execute() : [];
        $threads = $category->threadsPaginated;

        return view('forum::category.show', compact('categories', 'category', 'threads'));
    }

    public function store(StoreCategory $request, CreateCategory $action): RedirectResponse
    {
        $category = $action->execute($request->only('title', 'description', 'accepts_threads', 'is_private', 'color'));

        Forum::alert('success', 'categories.created');

        return redirect(Forum::route('category.show', $category));
    }

    public function update(Request $request, UpdateCategory $action): RedirectResponse
    {
        $action->execute($request->route('category'), $request->only('title', 'description', 'accepts_threads', 'is_private', 'color'));

        Forum::alert('success', 'categories.updated', 1);

        return redirect(Forum::route('category.show', $category));
    }

    public function destroy(Request $request, DeleteCategory $action): RedirectResponse
    {
        $action->execute($request->route('category'));

        Forum::alert('success', 'categories.deleted', 1);

        return redirect(config('forum.routing.prefix'));
    }

    public function manage(Request $request, GetAllCategories $action): View
    {
        $categories = $action->execute();
        $categories->makeHidden(['thread_count', 'post_count']);

        return view('forum::category.manage', ['categories' => $categories->toTree()]);
    }
}
