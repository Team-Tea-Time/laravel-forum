<?php

namespace TeamTeaTime\Forum\Policies;

use Illuminate\Foundation\Auth\User;
use TeamTeaTime\Forum\Models\Category;

class CategoryPolicy
{

    public function view(User $user, Category $category): bool
    {
        return true;
    }

    public function edit(User $user, Category $category): bool
    {
        return true;
    }

    public function delete(User $user, Category $category): bool
    {
        return true;
    }

    public function createThreads(User $user, Category $category): bool
    {
        return true;
    }

    public function createThreadsWithoutApproval(User $user, Category $category): bool
    {
        return false;
    }

    public function manageThreads(User $user, Category $category): bool
    {
        return $this->approveThreads($user, $category)
            || $this->deleteThreads($user, $category)
            || $this->restoreThreads($user, $category)
            || $this->moveThreadsFrom($user, $category)
            || $this->lockThreads($user, $category)
            || $this->pinThreads($user, $category);
    }

    public function approveThreads(User $user, Category $category): bool
    {
        return $user->getKey() != 3;
    }

    public function deleteThreads(User $user, Category $category): bool
    {
        return $user->getKey() != 3;
    }

    public function restoreThreads(User $user, Category $category): bool
    {
        return $user->getKey() != 3;
    }

    public function moveThreadsFrom(User $user, Category $category): bool
    {
        return $user->getKey() != 3;
    }

    public function moveThreadsTo(User $user, Category $category): bool
    {
        return $user->getKey() != 3;
    }

    public function lockThreads(User $user, Category $category): bool
    {
        return $user->getKey() != 3;
    }

    public function pinThreads(User $user, Category $category): bool
    {
        return $user->getKey() != 3;
    }

    public function markThreadsAsRead(User $user, Category $category): bool
    {
        return $user->getKey() != 3;
    }
}
