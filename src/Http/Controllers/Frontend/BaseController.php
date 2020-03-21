<?php

namespace TeamTeaTime\Forum\Http\Controllers\Frontend;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use TeamTeaTime\Forum\Support\Frontend\Forum;

abstract class BaseController extends Controller
{
    use AuthorizesRequests;

    /**
     * @param Collection|int $models
     */
    protected function bulkActionResponse($models, string $transKey): RedirectResponse
    {
        $count = is_int($models) ? $models : $models->count();

        if ($count)
        {
            Forum::alert('success', $transKey, $count);
        }
        else
        {
            Forum::alert('warning', 'general.invalid_selection');
        }

        return redirect()->back();
    }
}
