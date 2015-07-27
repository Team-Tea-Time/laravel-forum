<?php namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Riari\Forum\Events\UserViewingCategory;
use Riari\Forum\Events\UserViewingIndex;
use Riari\Forum\Models\Category;
use Riari\Forum\Repositories\Categories;

class CategoryController extends BaseController
{
    /**
     * @var Categories
     */
    protected $categories;

    /**
     * Create a category controller instance.
     *
     * @param  Categories  $categories
     */
    public function __construct(Categories $categories)
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

        return view('forum::category.index', ['categories' => $this->categories->getTop()]);
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

        return view('forum::category.show', compact('category'));
    }
}
