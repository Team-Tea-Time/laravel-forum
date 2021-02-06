<?php

namespace TeamTeaTime\Forum\Policies;

class ForumPolicy
{
    public function createCategories($user): bool
    {
        return $user->abilities()->contains('administrate');
    }

    public function manageCategories($user): bool
    {
        return $this->moveCategories($user) ||
               $this->renameCategories($user);
    }

    public function moveCategories($user): bool
    {
        return $user->abilities()->contains('administrate');
    }

    public function renameCategories($user): bool
    {
        return $user->abilities()->contains('administrate');
    }

    public function markThreadsAsRead($user): bool
    {
        return true;
    }

    public function viewTrashedThreads($user): bool
    {
        return $user->abilities()->contains('edit_forum');
    }

    public function viewTrashedPosts($user): bool
    {
        return $user->abilities()->contains('edit_forum');
    }
}
