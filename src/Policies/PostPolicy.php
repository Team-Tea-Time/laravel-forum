<?php

namespace TeamTeaTime\Forum\Policies;

use Illuminate\Support\Facades\Gate;
use TeamTeaTime\Forum\Models\Post;

class PostPolicy
{
    public function edit($user, Post $post): bool
    {
      if ($user->abilities()->contains('edit_forum')) { return true;}
        return $user->getKey() === $post->author_id;
    }

    public function delete($user, Post $post): bool
    {

        return $user->abilities()->contains('edit_forum');
    }

    public function restore($user, Post $post): bool
    {
        return $user->abilities()->contains('edit_forum');
    }
}
