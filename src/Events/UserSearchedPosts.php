<?php

namespace TeamTeaTime\Forum\Events;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use TeamTeaTime\Forum\Models\Category;

class UserSearchedPosts
{
    /** @var mixed */
    public $user;
    
    public ?Category $category;
    public string $term;
    public LengthAwarePaginator $results;

    public function __construct($user, ?Category $category, string $term, LengthAwarePaginator $results)
    {
        $this->category = $category;
        $this->term = $term;
        $this->results = $results;
    }
}
