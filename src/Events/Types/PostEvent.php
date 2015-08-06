<?php

namespace Riari\Forum\Events\Types;

use Riari\Forum\Models\Post;

class PostEvent
{
    /**
     * @var Post
     */
    public $post;

    /**
     * Create a new event instance.
     *
     * @param  Post  $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
