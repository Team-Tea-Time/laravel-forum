<?php

namespace TeamTeaTime\Forum\Policies;

class ForumPolicy
{
    public function createCategories($user): bool
    {
        return true;
    }

    public function moveCategories($user): bool
    {
        return true;
    }

    public function editCategories($user): bool
    {
        return true;
    }

    public function deleteCategories($user): bool
    {
        return true;
    }

    public function markThreadsAsRead($user): bool
    {
        return true;
    }

    public function viewTrashedThreads($user): bool
    {
        return true;
    }

    public function viewTrashedPosts($user): bool
    {
        return true;
    }
}
