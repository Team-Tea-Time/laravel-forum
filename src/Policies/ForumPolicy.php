<?php

namespace TeamTeaTime\Forum\Policies;

use Illuminate\Foundation\Auth\User;

class ForumPolicy
{
    public function createCategories(User $user): bool
    {
        return true;
    }

    public function moveCategories(User $user): bool
    {
        return true;
    }

    public function editCategories(User $user): bool
    {
        return true;
    }

    public function deleteCategories(User $user): bool
    {
        return true;
    }

    public function markThreadsAsRead(User $user): bool
    {
        return true;
    }

    public function approveThreads(User $user): bool
    {
        return true;
    }

    public function approvePosts(User $user): bool
    {
        return true;
    }

    public function viewTrashedThreads(User $user): bool
    {
        return true;
    }

    public function viewTrashedPosts(User $user): bool
    {
        return true;
    }
}
