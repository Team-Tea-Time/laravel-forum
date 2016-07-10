<?php namespace Riari\Forum\Support;

class Stats
{
    /**
     * Helper: update category thread and post counts.
     *
     * @return null
     */
    static function updateCategory($category)
    {
        // Update the category thread and post counts
        $threads = $category->threads()->where('deleted_at', null)->get();
        $category->thread_count = $threads->count();

        $postCount = 0;
        foreach ($threads as $thread) {
            $postCount += $thread->posts->count();
        }

        $category->post_count = $postCount;
        $category->saveWithoutTouch();
    }

    /**
     * Helper: update thread reply count.
     *
     * @return null
     */
    static function updateThread($thread)
    {
        // Update the category thread and post counts
        $postCount = $thread->posts->where('deleted_at', null)->count();
        $thread->reply_count = $postCount - 1;
        $thread->saveWithoutTouch();
    }
}
