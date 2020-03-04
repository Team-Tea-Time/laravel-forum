<?php

namespace TeamTeaTime\Forum\Http\Controllers\Frontend;

use Forum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

abstract class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected function bulkActionResponse(Collection $models, string $transKey): RedirectResponse
    {
        if ($models->count())
        {
            Forum::alert('success', $transKey, $models->count());
        }
        else
        {
            Forum::alert('warning', 'general.invalid_selection');
        }

        return redirect()->back();
    }
}
