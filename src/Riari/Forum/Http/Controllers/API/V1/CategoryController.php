<?php namespace Riari\Forum\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Riari\Forum\Models\Category;

class CategoryController extends BaseController
{
    /**
     * GET: return an index of categories.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $categories = ($request->has('top') && $request->input('top') == true)
            ? $this->categories->getTop()
            : $this->categories->paginate();

        return response()->json($categories);
    }

    /**
     * POST: create a new category.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $v = $this->validate($request, config('forum.preferences.validation.category'));

        if ($v instanceof JsonResponse) {
            return $v;
        }

        $category = $this->categories->create($request->only('category_id', 'title', 'subtitle', 'weight'));

        return response()->json(['data' => $category]);
    }

    /**
     * GET: return a category by ID.
     *
     * @param  Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Category $category)
    {
        return response()->json(['data' => $category]);
    }
}
