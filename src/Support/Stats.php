<?php

namespace TeamTeaTime\Forum\Support;

use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class Stats
{
    static function syncForCategory(Category $category): void
    {
        $threads = $category->threads()->where('deleted_at', null)->get();
        $category->thread_count = $threads->count();

        $postCount = 0;
        foreach ($threads as $thread)
        {
            $postCount += $thread->posts->count();
        }

        $category->post_count = $postCount;
        $category->saveWithoutTouch();
    }

    static function syncForThread(Thread $thread): void
    {
        $postCount = $thread->posts->where('deleted_at', null)->count();
        $thread->reply_count = $postCount - 1;
        $thread->saveWithoutTouch();
    }
}
