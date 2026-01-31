<?php

namespace TeamTeaTime\Forum\Http\Controllers\Api\Bulk;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use TeamTeaTime\Forum\Http\Controllers\Api\BaseController;
use TeamTeaTime\Forum\Http\Requests\Bulk\ReorderCategories;
use TeamTeaTime\Forum\Http\Resources\Category as CategoryResource;
use TeamTeaTime\Forum\Models\Category;

class CategoryController extends BaseController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Category::defaultOrder();
        $categories = $request->query('include_private') ? $query->get() : $query->where('is_private', false)->get();

        return CategoryResource::collection($categories);
    }

    public function fetch(Request $request): CategoryResource
    {
        return new CategoryResource($request->route('category'));
    }

    public function reorder(ReorderCategories $request): Response
    {
        $categories = $request->fulfill();

        return new Response(['categories' => CategoryResource::collection($categories)]);
    }
}
