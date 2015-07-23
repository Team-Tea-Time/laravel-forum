<?php namespace Riari\Forum\Http\Controllers\API\V1;

use Illuminate\Http\Request;

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
        if ($request->has('top') && $request->input('top') == true) {
            $categories = $this->categories->getTop()->toArray();
        } else {
            $categories = $this->categories->paginate()->toArray();
        }

        return response()->json($categories);
    }
}
