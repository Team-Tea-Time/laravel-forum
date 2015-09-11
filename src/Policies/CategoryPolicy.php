<?php

namespace Riari\Forum\Policies;

use Riari\Forum\Models\Category;

class CategoryPolicy
{
    /**
     * Permission: Create category.
     *
     * @param  object  $user
     * @param  Category  $category
     * @return bool
     */
    public function create($user, Category $category)
    {
        return false;
    }

    /**
     * Permission: Create thread.
     *
     * @param  object  $user
     * @param  Category  $category
     * @return bool
     */
    public function createThread($user, Category $category)
    {
        return true;
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
     * Permission: Move threads from/to category.
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
