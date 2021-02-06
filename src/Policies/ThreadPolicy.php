<?php

namespace TeamTeaTime\Forum\Policies;

use Illuminate\Support\Facades\Gate;
use TeamTeaTime\Forum\Models\Thread;

class ThreadPolicy
{
    public function deletePosts($user, Thread $thread): bool
    {
        return $user->abilities()->contains('edit_forum');
    }

    public function restorePosts($user, Thread $thread): bool
    {
        return $user->abilities()->contains('edit_forum');
    }

    public function rename($user, Thread $thread): bool
    {
      if ($user->abilities()->contains('edit_forum')) { return true;}
        return $user->getKey() === $thread->author_id;
    }

    public function reply($user, Thread $thread): bool
    {
        return ! $thread->locked;
    }

    public function delete($user, Thread $thread): bool
    {
        return $user->abilities()->contains('edit_forum');
    }

    public function restore($user, Thread $thread): bool
    {
        return $user->abilities()->contains('edit_forum');
    }
}
