<?php

namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Riari\Forum\Events\UserViewingCategory;
use Riari\Forum\Events\UserViewingIndex;
use Riari\Forum\Forum;

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
        $parameters = [
            'where' => ['category_id' => 0],
            'orderBy' => 'weight',
            'with' => ['children']
        ];

        if (Gate::allows('viewTrashedCategories')) {
            $parameters += ['include_deleted' => true];
        }

        $categories = $this->api('category.index')
                           ->parameters($parameters)
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
        $parameters = Gate::allows('viewTrashedCategories') ? ['include_deleted' => true] : [];
        $category = $this->api('category.fetch', $request->route('category'))->parameters($parameters)->get();

        event(new UserViewingCategory($category));

        $categories = [];
        if (Gate::allows('moveCategories')) {
            $categories = $this->api('category.index')->parameters(['where' => ['category_id' => 0]])->get();
        }

        return view('forum::category.show', compact('categories', 'category', 'threads'));
    }

    /**
     * POST: Store a new category.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $category = $this->api('category.store')->parameters($request->all())->post();

        Forum::alert('success', 'categories', 'created');

        return redirect($category->route);
    }

    /**
     * PATCH: Update a category.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        $action = $request->input('action');

        $category = $this->api("category.{$action}", $id)->parameters($request->all())->patch();

        Forum::alert('success', 'categories', 'updated', 1);

        return redirect($category->route);
    }

    /**
     * DELETE: Delete a category.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id, Request $request)
    {
        $category = $this->api('category.delete', $id)->parameters($request->all())->delete();

        Forum::alert('success', 'categories', 'deleted', 1);

        return redirect(config('forum.routing.root'));
    }
}
