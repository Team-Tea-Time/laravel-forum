<?php

namespace TeamTeaTime\Forum\Http\Controllers\Api\Bulk;

use Illuminate\Http\Request;
use TeamTeaTime\Forum\Http\Requests\Bulk\ManageCategories;

class CategoryController
{
    public function manage(ManageCategories $request)
    {
        $request->fulfill();

        return response(['success' => true]);
    }
}