<?php

namespace Riari\Forum\Policies;

use Riari\Forum\Models\Category;

class CategoryPolicy
{
    /**
     * Permission: Create threads in category.
     *
     * @param  object  $user
     * @param  Category  $category
     * @return bool
     */
    public function createThreads($user, Category $category)
    {
        return true;
    }

    /**
     * Permission: Manage threads in category.
     *
     * @param  object  $user
     * @param  Category  $category
     * @return bool
     */
    public function manageThreads($user, Category $category)
    {
        return $this->deleteThreads($user, $category) ||
               $this->moveThreads($user, $category) ||
               $this->lockThreads($user, $category) ||
               $this->pinThreads($user, $category);
    }

    /**
     * Permission: Delete threads in category.
     *
     * @param  object  $user
     * @param  Category  $category
     * @return bool
     */
    public function deleteThreads($user, Category $category)
    {
        return false;
    }

    /**
     * Permission: Move threads to category.
     *
     * @param  object  $user
     * @param  Category  $category
     * @return bool
     */
    public function moveThreads($user, Category $category)
    {
        return false;
    }

    /**
     * Permission: Lock threads in category.
     *
     * @param  object  $user
     * @param  Category  $category
     * @return bool
     */
    public function lockThreads($user, Category $category)
    {
        return false;
    }

    /**
     * Permission: Pin threads in category.
     *
     * @param  object  $user
     * @param  Category  $category
     * @return bool
     */
    public function pinThreads($user, Category $category)
    {
        return false;
    }

    /**
     * Permission: Rename category.
     *
     * @param  object  $user
     * @param  Category  $category
     * @return bool
     */
    public function rename($user, Category $category)
    {
        return false;
    }

    /**
     * Permission: Move category.
     *
     * @param  object  $user
     * @param  Category  $category
     * @return bool
     */
    public function move($user, Category $category)
    {
        return false;
    }

    /**
     * Permission: Delete category.
     *
     * @param  object  $user
     * @param  Category  $category
     * @return bool
     */
    public function delete($user, Category $category)
    {
        return false;
    }
}
