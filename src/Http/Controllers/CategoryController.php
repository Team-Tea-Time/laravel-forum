<?php

namespace Riari\Forum\Http\Controllers;

use Gate;
use Illuminate\Http\Request;
use Riari\Forum\Events\UserViewingCategory;
use Riari\Forum\Events\UserViewingIndex;
use Riari\Forum\Models\Category;

class CategoryController extends BaseController
{
    /**
     * @var Category
     */
    protected $categories;

    /**
     * Create a category controller instance.
     *
     * @param  Category  $categories
     */
    public function __construct(Category $categories)
    {
        $this->categories = $categories;
    }

    /**
     * GET: return an index of categories view (the forum index).
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        event(new UserViewingIndex);

        $categories = $this->categories
            ->where('category_id', null)
            ->with(['children', 'threads'])
            ->get();

        return view('forum::category.index', compact('categories'));
    }

    /**
     * GET: return a category view.
     *
     * @param  Category  $category
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category, Request $request)
    {
        event(new UserViewingCategory($category));

        $this->authorize($category);

        $threads = config('forum.preferences.list_trashed_threads')
            ? $category->threadsWithTrashedPaginated
            : $category->threadsPaginated;

        return view('forum::category.show', compact('category', 'threads'));
    }
}
