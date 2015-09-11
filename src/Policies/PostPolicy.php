<?php

namespace Riari\Forum\Policies;

use Riari\Forum\Models\Post;

class PostPolicy
{
    /**
     * Permission: Update post.
     *
     * @param  object  $user
     * @param  Post  $post
     * @return bool
     */
    public function update($user, Post $post)
    {
        return $user->id === $post->user_id;
    }

    /**
     * Permission: Delete post.
     *
     * @param  object  $user
     * @param  Post  $post
     * @return bool
     */
    public function delete($user, Post $post)
    {
        return $user->id === $post->user_id;
    }
}