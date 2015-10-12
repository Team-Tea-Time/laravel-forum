<?php

namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Riari\Forum\Events\UserViewingCategory;
use Riari\Forum\Events\UserViewingIndex;

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
        $categories = $this->api('category.index')
                           ->parameters(['with' => ['children']])
                           ->get();

        event(new UserViewingIndex);

        return view('forum::category.index', compact('categories'));
    }

    /**
     * GET: return a category view.
     *
     * @param  int  $categoryID
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($categoryID, Request $request)
    {
        $category = $this->api('category.show', $categoryID)->get();

        event(new UserViewingCategory($category));

        $threads = config('forum.preferences.list_trashed_threads')
            ? $category->threadsWithTrashedPaginated
            : $category->threadsPaginated;

        return view('forum::category.show', compact('category', 'threads'));
    }
}
