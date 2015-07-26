<?php namespace Riari\Forum\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\Repositories\Categories;

class CategoryController extends BaseController
{
    /**
     * @var Categories
     */
    protected $repository;

    /**
     * Create a new Category API controller instance.
     *
     * @param  Categories  $categories
     */
    public function __construct(Categories $categories)
    {
        $this->repository = $categories;

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
            ? $this->repository->getTop()
            : $this->repository->paginate();

        return $this->collectionResponse($categories);
    }
}
