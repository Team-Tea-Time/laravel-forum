<?php namespace Riari\Forum\Http\Controllers\Frontend;

use Forum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Riari\Forum\Events\UserViewingCategory;
use Riari\Forum\Events\UserViewingIndex;
use Riari\Forum\Http\Requests\StoreCategory;

use Riari\Forum\Services\CategoryService;

class CategoryController extends BaseController
{
    /** @var CategoryService */
    protected $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): View
    {
        $categories = $this->service->getAll()->toTree();
        // $categories = $this->api('category.index')
        //                    ->parameters(['where' => ['category_id' => 0], 'orderBy' => 'weight', 'orderDir' => 'asc', 'with' => ['categories', 'threads']])
        //                    ->get();

        event(new UserViewingIndex);

        return view('forum::category.index', compact('categories'));
    }

    public function show(Request $request): View
    {
        $category = $this->service->getByID($request->route('category'));

        event(new UserViewingCategory($category));

        $categories = [];
        if (Gate::allows('moveCategories')) {
            $categories = $this->service->getTopLevel();
        }

        $threads = $category->threadsPaginated;

        return view('forum::category.show', compact('categories', 'category', 'threads'));
    }

    public function store(StoreCategory $request): RedirectResponse
    {
        $category = $this->service->create(
            $request->only('title', 'description', 'accepts_threads', 'is_private', 'color')
        );

        // $category = $this->api('category.store')->parameters($request->all())->post();

        Forum::alert('success', 'categories.created');

        return redirect(Forum::route('category.show', $category));
    }

    public function update(Request $request): RedirectResponse
    {
        $action = $request->input('action');

        $category = $this->api("category.{$action}", $request->route('category'))->parameters($request->all())->patch();

        Forum::alert('success', 'categories.updated', 1);

        return redirect(Forum::route('category.show', $category));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->api('category.delete', $request->route('category'))->parameters($request->all())->delete();

        Forum::alert('success', 'categories.deleted', 1);

        return redirect(config('forum.routing.prefix'));
    }

    public function manage(Request $request): View
    {
        $categories = $this->service->getAll();
        $categories->makeHidden(['thread_count', 'post_count']);
        return view('forum::category.manage', ['categories' => $categories->toTree()]);
    }
}
