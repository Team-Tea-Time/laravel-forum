<?php

namespace Riari\Forum\Events\Types;

use Riari\Forum\Models\Post;

class PostEvent
{
    /** @var Post */
    public $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
