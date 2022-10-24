<?php

namespace TeamTeaTime\Forum\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use TeamTeaTime\Forum\Http\Requests\CreateCategory;
use TeamTeaTime\Forum\Http\Requests\DeleteCategory;
use TeamTeaTime\Forum\Http\Requests\UpdateCategory;
use TeamTeaTime\Forum\Http\Resources\CategoryResource;
use TeamTeaTime\Forum\Support\CategoryPrivacy;

class CategoryController extends BaseController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        if ($request->has('parent_id')) {
            $categories = CategoryPrivacy::getFilteredDescendantsFor($request->user(), $request->query('parent_id'));
        } else {
            $categories = CategoryPrivacy::getFilteredFor($request->user());
        }

        return CategoryResource::collection($categories);
    }

    public function fetch(Request $request): mixed
    {
        $category = $request->route('category');
        if (! $category->isAccessibleTo($request->user())) {
            return $this->notFoundResponse();
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
