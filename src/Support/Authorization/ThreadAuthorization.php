<?php

namespace TeamTeaTime\Forum\Support\Authorization;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use TeamTeaTime\Forum\{
    Models\Category,
    Models\Thread,
    Support\Access\CategoryAccess,
};

/**
 * ThreadAuthorization provides utilities for authorizing thread requests.
 */
class ThreadAuthorization
{
    public static function delete(User $user, Thread $thread): bool
    {
        return $user->can('deleteThreads', $thread->category) && $user->can('delete', $thread);
    }

    public static function restore(User $user, Thread $thread): bool
    {
        return $user->can('restoreThreads', $thread->category) && $user->can('restore', $thread);
    }

    public static function reply(User $user, Thread $thread): bool
    {
        return $user->can('reply', $thread);
    }

    public static function rename(User $user, Thread $thread): bool
    {
        return $user->can('rename', $thread);
    }

    public static function bulkDelete(User $user, array $threadIds): bool
    {
        $threads = Thread::whereIn('id', $threadIds)->with('category')->get();
        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($user);

        foreach ($threads as $thread) {
            $canView = $accessibleCategoryIds->contains($thread->category_id) && $user->can('view', $thread);
            $canDelete = $user->can('deleteThreads', $thread->category) && $user->can('delete', $thread);

            if (!($canView && $canDelete)) {
                return false;
            }
        }

        return true;
    }

    public static function bulkLock(User $user, array $threadIds): bool
    {
        $categories = CategoryAccess::getFilteredCategoryCollectionFor($user, $threadIds);

        foreach ($categories as $category) {
            if (!$user->can('lockThreads', $category)) {
                return false;
            }
        }

        return true;
    }

    public static function bulkMove(User $user, Collection $sourceCategories, Category $destinationCategory): bool
    {
        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($user);

        if (!($accessibleCategoryIds->contains($destinationCategory->id) || $user->can('moveThreadsTo', $destinationCategory))) {
            return false;
        }

        foreach ($sourceCategories as $category) {
            if (!($accessibleCategoryIds->contains($category->id) || $user->can('moveThreadsFrom', $category))) {
                return false;
            }
        }

        return true;
    }

    public static function bulkPin(User $user, array $threadIds): bool
    {
        $categories = CategoryAccess::getFilteredCategoryCollectionFor($user, $threadIds);

        foreach ($categories as $category) {
            if (!$user->can('pinThreads', $category)) {
                return false;
            }
        }

        return true;
    }

    public static function bulkRestore(User $user, array $threadIds): bool
    {
        if (!$user->can('viewTrashedThreads')) {
            return false;
        }

        $threads = Thread::whereIn('id', $threadIds)->get();
        foreach ($threads as $thread) {
            if (!($user->can('restoreThreads', $thread->category) && $user->can('restore', $thread))) {
                return false;
            }
        }

        return true;
    }
}
