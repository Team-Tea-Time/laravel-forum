<?php

namespace TeamTeaTime\Forum\Http\Controllers\Api;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use TeamTeaTime\Forum\Http\Resources\CategoryResource;
use TeamTeaTime\Forum\Models\Category;

class CategoryController
{
    use AuthorizesRequests;

    public function index(): AnonymousResourceCollection
    {
        $categories = Category::defaultOrder()->get()->filter(function ($category)
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