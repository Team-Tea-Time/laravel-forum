<?php

namespace TeamTeaTime\Forum\Events;

class UserManagedCategories
{
    /** @var mixed */
    public $user;

    public array $categories;
    public int $numCategoriesAffected;

    public function __construct($user, array $categories, int $numCategoriesAffected)
    {
        $this->user = $user;
        $this->categories = $categories;
        $this->numCategoriesAffected = $numCategoriesAffected;
    }
}