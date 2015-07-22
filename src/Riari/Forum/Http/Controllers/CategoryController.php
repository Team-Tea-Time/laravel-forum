<?php namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Riari\Forum\Models\Category;

class CategoryController extends BaseController
{
    /**
     * GET: return an index of categories view (the forum index).
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
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
        return view('forum::category.show', compact('category'));
    }
}
