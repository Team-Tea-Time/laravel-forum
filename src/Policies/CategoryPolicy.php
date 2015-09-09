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
     * Permission: View category.
     *
     * @param  object  $user
     * @param  Category  $category
     * @return bool
     */
    public function show($user, Category $category)
    {
        return true;
    }

    /**
     * Permission: Update category.
     *
     * @param  object  $user
     * @param  Category  $category
     * @return bool
     */
    public function update($user, Category $category)
    {
        return $user->id === $thread->user_id;
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
        return $user->id === $thread->user_id;
    }
}
