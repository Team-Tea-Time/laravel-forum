<?php

namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
                           ->parameters(['where' => ['category_id' => null]], ['orderBy' => 'weight'], ['with' => ['children']])
                           ->get();

        event(new UserViewingIndex);

        return view('forum::category.index', compact('categories'));
    }

    /**
     * GET: return a category view.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $category = $this->api('category.fetch', $request->route('category'))->get();

        event(new UserViewingCategory($category));

        $threads = config('forum.preferences.list_trashed_threads')
            ? $category->threadsWithTrashedPaginated
            : $category->threadsPaginated;

        $categories = [];
        if (Gate::allows('moveThreads', $category)) {
            $categories = $this->api('category.index')->parameters(['where' => ['category_id' => null]], ['where' => ['allows_threads' => 1]])->get();
        }

        return view('forum::category.show', compact('categories', 'category', 'threads'));
    }
}
