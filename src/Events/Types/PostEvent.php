<?php

namespace TeamTeaTime\Forum\Events\Types;

use TeamTeaTime\Forum\Models\Post;

class PostEvent
{
    /** @var mixed */
    public $user;

    /** @var Post */
    public $post;

    public function __construct($user, Post $post)
    {
        $this->user = $user;
        $this->post = $post;
    }
}
