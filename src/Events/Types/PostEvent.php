<?php

namespace TeamTeaTime\Forum\Events\Types;

use TeamTeaTime\Forum\Models\Post;

class PostEvent
{
    /** @var Post */
    public $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
