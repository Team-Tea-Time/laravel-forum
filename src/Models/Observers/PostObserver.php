<?php namespace TeamTeaTime\Forum\Models\Observers;

use Carbon\Carbon;
use TeamTeaTime\Forum\Support\Stats;

class PostObserver
{
    public function created($post)
    {
        // Update the thread's updated_at timestamp to match the created_at timestamp of the new post
        $post->thread->updated_at = $post->created_at;
        $post->thread->save();

        // Set the post's sequence number
        $post->sequence = $post->getSequenceNumber();
        $post->saveWithoutTouch();

        // Update the thread reply count if this is not the first post in the thread
        if ($post->thread->posts->count() > 1)
        {
            $post->thread->reply_count += 1;
            $post->thread->saveWithoutTouch();
        }

        // Update the post count on the category
        $category = $post->thread->category;
        $category->post_count += 1;
        $category->saveWithoutTouch();
    }

    public function deleted($post)
    {
        if (! is_null($post->children))
        {
            // Other posts reference this one, so set their parent post IDs to 0
            $post->children()->update(['post_id' => 0]);
        }

        // Update sequence numbers for all of the thread's posts
        $post->thread->posts->each(function ($post)
        {
            $post->sequence = $post->getSequenceNumber();
            $post->saveWithoutTouch();
        });

        // Update the thread's updated_at timestamp to match the created_at timestamp of its last post
        $post->thread->updated_at = $post->thread->getLastPost()->created_at;
        $post->thread->save();

        Stats::syncForThread($post->thread);
        Stats::syncForCategory($post->thread->category);
    }

    public function restored($post)
    {
        Stats::syncForThread($post->thread);
        Stats::syncForCategory($post->thread->category);
    }
}
