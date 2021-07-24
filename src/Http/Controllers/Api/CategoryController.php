<?php

namespace TeamTeaTime\Forum\Http\Controllers\Api;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use TeamTeaTime\Forum\Http\Resources\CategoryResource;
use TeamTeaTime\Forum\Models\Category;

class CategoryController
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Category::defaultOrder();
        $parentId = $request->query('parent_id');

        if ($parentId !== null)
        {
            $query = $parentId == 0
                ? $query->whereNull('parent_id')
                : $query->where('parent_id', $request->query('parent_id'));
        }

        $categories = $query->get()->filter(function ($category)
        {
            if ($category->is_private) return Gate::allows('view', $category);

            return true;
        });

        return CategoryResource::collection($categories);
    }

    public function fetch(Category $category): CategoryResource
    {
        $this->authorize('view', $category);

        return new CategoryResource($category);
    }
}