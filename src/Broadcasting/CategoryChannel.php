<?php

namespace TeamTeaTime\Forum\Broadcasting;

use Illuminate\Foundation\Auth\User;
use TeamTeaTime\Forum\Models\Category;

class CategoryChannel
{
    public function join(User $user, int $id): array|bool
    {
        $category = Category::find($id);
        $can = $user->can('view', $category);
        return $can;
    }
}
