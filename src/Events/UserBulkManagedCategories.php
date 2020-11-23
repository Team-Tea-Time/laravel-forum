<?php

namespace TeamTeaTime\Forum\Events;

class UserBulkManagedCategories
{
    /** @var mixed */
    public $user;

    public array $categories;

    public function __construct($user, array $categories)
    {
        $this->user = $user;
        $this->categories = $categories;
    }
}