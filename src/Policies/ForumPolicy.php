<?php

namespace Riari\Forum\Policies;

use Riari\Forum\Models\Category;

class ForumPolicy
{
    /**
     * Permission: Create categories.
     *
     * @param  object  $user
     * @return bool
     */
    public function createCategories($user)
    {
        return true;
    }

    /**
     * Permission: Manage category.
     *
     * @param  object  $user
     * @return bool
     */
    public function manageCategories($user)
    {
        return $this->deleteCategories($user) ||
               $this->moveCategories($user) ||
               $this->renameCategories($user);
    }

    /**
     * Permission: Delete categories.
     *
     * @param  object  $user
     * @return bool
     */
    public function deleteCategories($user)
    {
        return true;
    }

    /**
     * Permission: Move categories.
     *
     * @param  object  $user
     * @return bool
     */
    public function moveCategories($user)
    {
        return true;
    }

    /**
     * Permission: Rename categories.
     *
     * @param  object  $user
     * @return bool
     */
    public function renameCategories($user)
    {
        return true;
    }

    /**
     * Permission: View trashed categories.
     *
     * @param  object  $user
     * @return bool
     */
    public function viewTrashedCategories($user)
    {
        return true;
    }

    /**
     * Permission: View trashed threads.
     *
     * @param  object  $user
     * @return bool
     */
    public function viewTrashedThreads($user)
    {
        return true;
    }

    /**
     * Permission: View trashed posts.
     *
     * @param  object  $user
     * @return bool
     */
    public function viewTrashedPosts($user)
    {
        return true;
    }
}
