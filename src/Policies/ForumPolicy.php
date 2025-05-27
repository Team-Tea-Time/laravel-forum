<?php

namespace TeamTeaTime\Forum\Policies;

class ForumPolicy
{
    public function createCategories($user): bool
    {
        return $user->getKey() != 3;
    }

    public function moveCategories($user): bool
    {
        return $user->getKey() != 3;
    }

    public function editCategories($user): bool
    {
        return $user->getKey() != 3;
    }

    public function deleteCategories($user): bool
    {
        return $user->getKey() != 3;
    }

    public function markThreadsAsRead($user): bool
    {
        return $user->getKey() != 3;
    }

    public function approveThreads($user): bool
    {
        return $user->getKey() != 3;
    }

    public function approvePosts($user): bool
    {
        return $user->getKey() != 3;
    }

    public function viewTrashedThreads($user): bool
    {
        return $user->getKey() != 3;
    }

    public function viewTrashedPosts($user): bool
    {
        return $user->getKey() != 3;
    }
}
