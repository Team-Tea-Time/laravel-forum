<?php

namespace Riari\Forum\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\API\Cache;
use Riari\Forum\Models\Category;

class CategoryController extends BaseController
{
    /**
     * Return the model to use for this controller.
     *
     * @return Category
     */
    protected function model()
    {
        return new Category;
    }

    /**
     * Return the translation file name to use for this controller.
     *
     * @return string
     */
    protected function translationFile()
    {
        return 'categories';
    }

    /**
     * GET: return an index of categories.
     *
     * @param  Request  $request
     * @return JsonResponse|Response
     */
    public function index(Request $request)
    {
        $this->validate($request, ['category_id' => 'integer|exists:forum_categories,id']);

        $categories = new Category;
        if ($request->has('where')) $categories = $categories->where($request->input('where'));
        if ($request->has('with')) $categories = $categories->with($request->input('with'));

        return $this->response($categories->get());
    }

    /**
     * POST: create a new category model.
     *
     * @return JsonResponse|Response
     */
    public function store()
    {
        $this->authorize('createCategories');

        parent::store($request);
    }
}
