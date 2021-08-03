<?php

namespace TeamTeaTime\Forum\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use TeamTeaTime\Forum\Http\Requests\CreateCategory;
use TeamTeaTime\Forum\Http\Requests\DeleteCategory;
use TeamTeaTime\Forum\Http\Requests\UpdateCategory;
use TeamTeaTime\Forum\Http\Resources\CategoryResource;
use TeamTeaTime\Forum\Models\Category;

class CategoryController extends BaseController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Category::defaultOrder();
        $parentId = $request->query('parent_id');

        if ($parentId !== null) {
            $query = $parentId == 0
                ? $query->whereNull('parent_id')
                : $query->where('parent_id', $request->query('parent_id'));
        }

        $categories = $query->get()->filter(function ($category) use ($request) {
            return ! $category->is_private || $request->user() && $request->user()->can('view', $category);
        });

        return CategoryResource::collection($categories);
    }

    public function fetch(Category $category): CategoryResource
    {
        if ($category->is_private) {
            $this->authorize('view', $category);
        }

        return new CategoryResource($category);
    }

    public function store(CreateCategory $request): CategoryResource
    {
        $category = $request->fulfill();

        return new CategoryResource($category);
    }

    public function update(UpdateCategory $request): CategoryResource
    {
        $category = $request->fulfill();

        return new CategoryResource($category);
    }

    public function delete(DeleteCategory $request): Response
    {
        $request->fulfill();

        return new Response(['success' => true], 200);
    }
}
