<?php

namespace TeamTeaTime\Forum\Events;

use Illuminate\Database\Eloquent\Collection;
use TeamTeaTime\Forum\Events\Types\CollectionEvent;
use TeamTeaTime\Forum\Models\Category;

class UserBulkMovedThreads extends CollectionEvent
{
    public Category $destinationCategory;

    public function __construct($user, Collection $threads, Category $destinationCategory)
    {
        parent::__construct($user, $threads);

        $this->destinationCategory = $destinationCategory;
    }
}