<?php

namespace TeamTeaTime\Forum\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;

abstract class BaseController
{
    use AuthorizesRequests;

    protected function invalidSelectionResponse(): Response
    {
        return new Response([
            'success' => false,
            'message' => trans('forum::general.invalid_selection')
        ], 403);
    }
}