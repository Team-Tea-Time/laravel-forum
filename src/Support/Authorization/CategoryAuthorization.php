<?php

namespace TeamTeaTime\Forum\Support\Authorization;

use Illuminate\Foundation\Auth\User;
use TeamTeaTime\Forum\Models\Category;

/**
 * CategoryAuthorization provides utilities for authorizing category requests.
 */
class CategoryAuthorization
{
    public static function move(User $user): bool
    {
        return $user->can('moveCategories');
    }

    public static function create(User $user): bool
    {
        return $user->can('createCategories');
    }

    public static function edit(User $user, Category $category): bool
    {
        return $user->can('editCategories') && $user->can('edit', $category);
    }

    public static function delete(User $user, Category $category): bool
    {
        return $user->can('deleteCategories') && $user->can('delete', $category);
    }

    public static function createThreads(User $user, Category $category): bool
    {
        return $category->accepts_threads && $user->can('createThreads', $category);
    }

    public static function moveThread(User $user, Category $sourceCategory, Category $destinationCategory): bool
    {
        return $user->can('moveThreadsFrom', $sourceCategory) && $user->can('moveThreadsTo', $destinationCategory);
    }

    public static function lockThreads(User $user, Category $category): bool
    {
        return $user->can('lockThreads', $category);
    }

    public static function pinThreads(User $user, Category $category): bool
    {
        return $user->can('pinThreads', $category);
    }

    public static function markThreadsAsRead(User $user, ?Category $category): bool
    {
        if ($category !== null && !$category->isAccessibleTo($user)) {
            return false;
        }

        return $user->can('markThreadsAsRead', $category);
    }

    public static function search(User $user, ?Category $category): bool
    {
        return $category == null || !$category->is_private || $category->isAccessibleTo($user);
    }
}
