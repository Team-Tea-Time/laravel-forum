<?php namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Riari\Forum\Models\Category;

class CategoryController extends BaseController
{
    public function index()
    {
        return view('forum::index', ['categories' => $this->categories->getTop()]);
    }

    public function show(Category $category)
    {
        return view('forum::category.show', compact('category'));
    }
}
