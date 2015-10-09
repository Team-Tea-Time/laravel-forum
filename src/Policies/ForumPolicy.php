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
        return false;
    }
}
