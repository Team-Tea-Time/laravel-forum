<?php namespace TeamTeaTime\Forum\Models\Observers;

use Carbon\Carbon;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Support\Stats;

class ThreadObserver
{
    public function created($thread)
    {
        $thread->category->increment('thread_count');
    }

    public function updating($thread)
    {
        if ($thread->getOriginal('category_id') != $thread->category_id)
        {
            $oldCategory = Category::find($thread->getOriginal('category_id'));
            $newCategory = Category::find($thread->category_id);
            $postCount = $thread->posts->count();

            $oldCategory->thread_count -= 1;
            $oldCategory->post_count -= $postCount;
            $oldCategory->save();

            $newCategory->thread_count += 1;
            $newCategory->post_count += $postCount;
            $newCategory->save();
        }
    }

    public function deleted($thread)
    {
        if (! $thread->deleted_at || $thread->deleted_at->toDateTimeString() !== Carbon::now()->toDateTimeString())
        {
            // The thread was force-deleted, so the posts should be too
            $thread->posts()->withTrashed()->forceDelete();

            $thread->readers()->detach();
        }

        Stats::syncForCategory($thread->category);
    }

    public function restored($thread)
    {
        Stats::syncForThread($thread);
        Stats::syncForCategory($thread->category);
    }
}
