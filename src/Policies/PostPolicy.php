<?php

namespace TeamTeaTime\Forum\Policies;

use Illuminate\Foundation\Auth\User;
use TeamTeaTime\Forum\Models\Post;

class PostPolicy
{
    public function edit(User $user, Post $post): bool
    {
        return $user->getKey() === $post->author_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->getKey() === $post->author_id;
    }

    public function restore(User $user, Post $post): bool
    {
        return $user->getKey() === $post->author_id;
    }
}
