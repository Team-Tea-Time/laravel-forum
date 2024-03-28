<?php

namespace TeamTeaTime\Forum\Support\Access;

use Illuminate\Foundation\Auth\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use TeamTeaTime\Forum\Models\Category;

/**
 * ThreadAccess provides utilities for retrieving thread data based on user authorisation.
 */
class ThreadAccess
{
    public static function getSelectableThreadIdsFor(?User $user, LengthAwarePaginator $threads, Category $category): array
    {
        $threadIds = [];

        if (!$user) return $threadIds;

        if (Gate::any(['moveThreadsFrom', 'lockThreads', 'pinThreads'], $category)) {
            // There are no thread-specific abilities corresponding to these,
            // so we can include all of the threads for this page
            $threadIds = $threads->pluck('id')->toArray();
        } else {
            $canDeleteThreads = $user->can('deleteThreads', $category);
            $canRestoreThreads = $user->can('restoreThreads', $category);

            if ($canDeleteThreads || $canRestoreThreads) {
                foreach ($threads as $thread) {
                    if (($canDeleteThreads && $user->can('delete', $thread))
                        || $canRestoreThreads && $user->can('restore', $thread)
                    ) {
                        $threadIds[] = $thread->id;
                    }
                }
            }
        }

        return $threadIds;
    }
}
