<?php

namespace TeamTeaTime\Forum\Policies;

use Illuminate\Foundation\Auth\User;
use TeamTeaTime\Forum\Models\Thread;

class ThreadPolicy
{
    public function view(User $user, Thread $thread): bool
    {
        return $thread->isApproved || $user->getKey() == 1 || $user->getKey() == 2;
    }

    public function rename(User $user, Thread $thread): bool
    {
        return $user->getKey() === $thread->author_id;
    }

    public function reply(User $user, Thread $thread): bool
    {
        return !$thread->locked;
    }

    public function replyWithoutApproval(User $user, Thread $thread): bool
    {
        return $user->getKey() === $thread->author_id;
    }

    public function delete(User $user, Thread $thread): bool
    {
        return true;
        return $user->getKey() === $thread->author_id;
    }

    public function restore(User $user, Thread $thread): bool
    {
        return $user->getKey() === $thread->author_id;
    }

    public function approvePosts(User $user, Thread $thread): bool
    {
        return true;
    }

    public function deletePosts(User $user, Thread $thread): bool
    {
        return true;
    }

    public function restorePosts(User $user, Thread $thread): bool
    {
        return true;
    }
}
