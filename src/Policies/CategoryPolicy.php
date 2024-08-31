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
        return true;
    }

    public function deleteThreads(User $user, Category $category): bool
    {
        return true;
    }

    public function restoreThreads(User $user, Category $category): bool
    {
        return true;
    }

    public function moveThreadsFrom(User $user, Category $category): bool
    {
        return true;
    }

    public function moveThreadsTo(User $user, Category $category): bool
    {
        return true;
    }

    public function lockThreads(User $user, Category $category): bool
    {
        return true;
    }

    public function pinThreads(User $user, Category $category): bool
    {
        return true;
    }

    public function markThreadsAsRead(User $user, Category $category): bool
    {
        return true;
    }
}
