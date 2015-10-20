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

        $categories = ($request->input('paginate')) ? $this->model->paginate() : $this->model->get();

        return $this->response($categories);
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
