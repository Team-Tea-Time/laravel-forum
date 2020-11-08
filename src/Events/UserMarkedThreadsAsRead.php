<?php

namespace TeamTeaTime\Forum\Events;

use Illuminate\Database\Eloquent\Collection;
use TeamTeaTime\Forum\Models\Category;

class UserMarkedThreadsAsRead
{
    /** @var mixed */
    public $user;

    public Catgory $category;
    public Collection $threads;

    public function __construct($user, Category $category, Collection $threads)
    {
        $this->user = $user;
        $this->category = $category;
        $this->threads = $threads;
    }
}
