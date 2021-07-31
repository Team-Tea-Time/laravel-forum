<?php

namespace TeamTeaTime\Forum\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;

trait HasForumContent
{
    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class, 'author_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }
}
