<?php namespace Riari\Forum\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\Model\Category;

class CategoryController extends BaseController
{
    /**
     * @var Category
     */
    protected $model;

    /**
     * Create a new Category API controller instance.
     *
     * @param  Categories  $categories
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
