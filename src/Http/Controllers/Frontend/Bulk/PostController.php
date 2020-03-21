<?php

namespace TeamTeaTime\Forum\Http\Controllers\Frontend\Bulk;

use Illuminate\Http\RedirectResponse;
use TeamTeaTime\Forum\Http\Controllers\Frontend\BaseController;
use TeamTeaTime\Forum\Http\Requests\Bulk\DestroyPosts;
use TeamTeaTime\Forum\Http\Requests\Bulk\RestorePosts;

class PostController extends BaseController
{
    public function destroy(DestroyPosts $request): RedirectResponse
    {
        $posts = $request->fulfill();

        return $this->bulkActionResponse($posts, 'posts.deleted');
    }

    public function restore(RestorePosts $request): RedirectResponse
    {
        $posts = $request->fulfill();

        return $this->bulkActionResponse($posts, 'posts.updated');
    }
}
