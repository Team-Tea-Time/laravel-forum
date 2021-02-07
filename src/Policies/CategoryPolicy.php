<?php

namespace TeamTeaTime\Forum\Policies;

use TeamTeaTime\Forum\Models\Category;

class CategoryPolicy
{
    public function createThreads($user, Category $category): bool
    {
      if (str_contains($category->title,'News') || str_contains($category->title,'Neuigkeiten'))
      {
        return $user->abilities()->contains('administrate');
      }
        return true;
    }

    public function manageThreads($user, Category $category): bool
    {
        return $this->deleteThreads($user, $category) ||
               $this->enableThreads($user, $category) ||
               $this->moveThreadsFrom($user, $category) ||
               $this->lockThreads($user, $category) ||
               $this->pinThreads($user, $category);
    }

    public function deleteThreads($user, Category $category): bool
    {
        return $user->abilities()->contains('edit_forum');
    }

    public function restoreThreads($user, Category $category): bool
    {
        return $user->abilities()->contains('edit_forum');
    }

    public function enableThreads($user, Category $category): bool
    {
        return $user->abilities()->contains('administrate');
    }

    public function moveThreadsFrom($user, Category $category): bool
    {
        return $user->abilities()->contains('edit_forum');
    }

    public function moveThreadsTo($user, Category $category): bool
    {
        return $user->abilities()->contains('edit_forum');
    }

    public function lockThreads($user, Category $category): bool
    {
        return $user->abilities()->contains('edit_forum');
    }

    public function pinThreads($user, Category $category): bool
    {
        return $user->abilities()->contains('edit_forum');
    }

    public function markThreadsAsRead($user, Category $category): bool
    {
        return $user->abilities()->contains('edit_forum');
    }

    public function view($user, Category $category): bool
    {
        return true;
    }

    public function delete($user, Category $category): bool
    {
        return $user->abilities()->contains('administrate');
    }

    public function restore($user, Category $category): bool
    {
        return $user->abilities()->contains('administrate');
    }
}
