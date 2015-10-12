<?php

namespace Riari\Forum\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\Models\Category;

class CategoryController extends BaseController
{
    /**
     * Create a new Category API controller instance.
     *
     * @param  Category  $model
     * @param  Request  $request
     */
    public function __construct(Category $model, Request $request)
    {
        parent::__construct($model, $request);

        $rules = config('forum.preferences.validation');
        $this->rules = [
            'store' => array_merge_recursive(
                $rules['base'],
                $rules['post|put']['category']
            ),
            'update' => array_merge_recursive(
                $rules['base'],
                $rules['patch']['category']
            )
        ];

        $this->translationFile = 'categories';
    }

    /**
     * GET: return an index of categories.
     *
     * @return JsonResponse|Response
     */
    public function index()
    {
        $this->validate(['category_id' => 'integer|exists:forum_categories,id']);

        $categories = $this->model->where('category_id', $this->request->input('category_id'));
        $categories = $this->request->has('with') ? $categories->with($this->request->input('with'))->get() : $categories->get();

        return $this->collectionResponse($categories);
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
