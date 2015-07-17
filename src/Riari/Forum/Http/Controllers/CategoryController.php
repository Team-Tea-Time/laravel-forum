<?php namespace Riari\Forum\Http\Controllers;

use Illuminate\Routing\Controller;
use Riari\Forum\Models\Category;
use Riari\Forum\Repositories\Categories;

class CategoryController extends BaseController
{
    /**
     * @var Categories
     */
    protected $categories;

    /**
     * Create a new category controller instance.
     *
     * @param  Categories  $categories
     */
    public function __construct(Categories $categories)
    {
        $this->categories = $categories;
    }

    public function index()
    {
        return view('forum::index', ['categories' => $this->categories->getTop()]);
    }

    public function show(Category $category)
    {
        return view('forum::category.show', compact('category'));
    }
}
