<?php

namespace TeamTeaTime\Forum\Http\Controllers\Blade\Bulk;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use TeamTeaTime\Forum\Http\Controllers\Blade\BaseController;
use TeamTeaTime\Forum\Http\Requests\Bulk\ReorderCategories;
use TeamTeaTime\Forum\Support\Web\Forum;

class CategoryController extends BaseController
{
    public function reorder(ReorderCategories $request): JsonResponse
    {
        $request->fulfill();

        return Response::json(['status' => 'success']);
    }
}
