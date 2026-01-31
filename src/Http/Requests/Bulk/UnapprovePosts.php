<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use TeamTeaTime\Forum\Actions\Bulk\UnapprovePosts as Action;
use TeamTeaTime\Forum\Events\UserBulkUnapprovedPosts;

class UnapprovePosts extends ApprovePosts
{
    public function fulfill()
    {
        $action = new Action($this->validated()['posts']);
        $posts = $action->execute();

        if ($posts !== null) {
            UserBulkUnapprovedPosts::dispatch($this->user(), $posts);
        }

        return $posts;
    }
}
