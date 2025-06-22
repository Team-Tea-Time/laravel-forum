<?php

namespace TeamTeaTime\Forum\Http\Controllers\Blade\Bulk;

use Illuminate\Http\RedirectResponse;
use TeamTeaTime\Forum\Http\Controllers\Blade\BaseController;
use TeamTeaTime\Forum\Http\Requests\Bulk\ApprovePosts;
use TeamTeaTime\Forum\Http\Requests\Bulk\DeletePosts;
use TeamTeaTime\Forum\Http\Requests\Bulk\RestorePosts;
use TeamTeaTime\Forum\Http\Requests\Bulk\UnapprovePosts;

class PostController extends BaseController
{
    public function approve(ApprovePosts $request): RedirectResponse
    {
        $result = $request->fulfill();

        if ($result === null) {
            return $this->invalidSelectionResponse();
        }

        return $this->bulkActionResponse($result->count(), 'posts.approved');
    }

    public function unapprove(UnapprovePosts $request): RedirectResponse
    {
        $result = $request->fulfill();

        if ($result === null) {
            return $this->invalidSelectionResponse();
        }

        return $this->bulkActionResponse($result->count(), 'posts.unapproved');
    }

    public function delete(DeletePosts $request): RedirectResponse
    {
        $result = $request->fulfill();

        if ($result === null) {
            return $this->invalidSelectionResponse();
        }

        return $this->bulkActionResponse($result->count(), 'posts.deleted');
    }

    public function restore(RestorePosts $request): RedirectResponse
    {
        $result = $request->fulfill();

        if ($result === null) {
            return $this->invalidSelectionResponse();
        }

        return $this->bulkActionResponse($result->count(), 'posts.updated');
    }
}
