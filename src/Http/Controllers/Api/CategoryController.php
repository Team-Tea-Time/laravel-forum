<?php

namespace TeamTeaTime\Forum\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use TeamTeaTime\Forum\Http\Requests\CreateCategory;
use TeamTeaTime\Forum\Http\Requests\DeleteCategory;
use TeamTeaTime\Forum\Http\Requests\EditCategory;
use TeamTeaTime\Forum\Http\Resources\CategoryResource;
use TeamTeaTime\Forum\Support\Access\CategoryAccess;

class CategoryController extends BaseController
{
    protected $resourceClass = null;

    public function __construct()
    {
        $this->resourceClass = config('forum.api.resources.category', CategoryResource::class);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        if ($request->has('parent_id')) {
            $categories = CategoryAccess::getFilteredDescendantsFor($request->user(), $request->query('parent_id'));
        } else {
            $categories = CategoryAccess::getFilteredTreeFor($request->user());
        }

        return $this->resourceClass::collection($categories);
    }

    public function fetch(Request $request): JsonResource|Response
    {
        $category = $request->route('category');
        if (!$category->isAccessibleTo($request->user())) {
            return $this->notFoundResponse();
        }

        return new $this->resourceClass($category);
    }

    public function store(CreateCategory $request): JsonResource
    {
        $category = $request->fulfill();

        return new $this->resourceClass($category);
    }

    public function update(EditCategory $request): JsonResource
    {
        $category = $request->fulfill();

        return new $this->resourceClass($category);
    }

    public function delete(DeleteCategory $request): Response
    {
        $request->fulfill();

        return new Response(['success' => true], 200);
    }
}
