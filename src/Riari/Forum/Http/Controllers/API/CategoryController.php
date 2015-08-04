<?php namespace Riari\Forum\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\Models\Category;

class CategoryController extends BaseController
{
    /**
     * Create a new Category API controller instance.
     *
     * @param  Category  $model
     */
    public function __construct(Category $model)
    {
        $this->model = $model;

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
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $categories = ($request->has('top') && $request->input('top') == true)
            ? $this->model->where('category_id', null)->get()
            : $this->model->paginate();

        return $this->collectionResponse($categories);
    }
}
