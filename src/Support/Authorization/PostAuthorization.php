<?php

namespace TeamTeaTime\Forum\Support\Authorization;

use Illuminate\Foundation\Auth\User;
use TeamTeaTime\Forum\{
    Models\Post,
    Support\Access\CategoryAccess,
};

/**
 * PostAuthorization provides utilities for authorizing post requests.
 */
class PostAuthorization
{
    public static function edit(User $user, Post $post): bool
    {
        return $user->can('edit', $post);
    }

    public static function delete(User $user, Post $post): bool
    {
        return $post->sequence > 1 && $user->can('deletePosts', $post->thread) && $user->can('delete', $post);
    }

    public static function restore(User $user, Post $post): bool
    {
        return $user->can('restorePosts', $post->thread) && $user->can('restore', $post);
    }

    public static function bulkDelete(User $user, array $postIds): bool
    {
        $query = Post::query();

        if ($user->can('viewTrashedPosts')) {
            $query = $query->withTrashed();
        }

        $posts = $query->with(['thread', 'thread.category'])->whereIn('id', $postIds);

        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($user);

        foreach ($posts as $post) {
            $canView = $accessibleCategoryIds->contains($post->thread->category_id) && $user->can('view', $post->thread);
            $canDelete = $user->can('deletePosts', $post->thread) && $user->can('delete', $post);

            if (!($canView && $canDelete)) {
                return false;
            }
        }

        return true;
    }

    public static function bulkRestore(User $user, array $postIds): bool
    {
        $posts = Post::whereIn('id', $postIds)->onlyTrashed()->get();

        foreach ($posts as $post) {
            if (!($user->can('restorePosts', $post->thread) && $user->can('restore', $post))) {
                return false;
            }
        }

        return true;
    }
}
