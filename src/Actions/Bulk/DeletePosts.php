<?php

namespace TeamTeaTime\Forum\Actions\Bulk;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\{
    Actions\BaseAction,
    Models\Category,
    Models\Post,
    Models\Thread,
};

class DeletePosts extends BaseAction
{
    private array $postIds;
    private bool $includeTrashed;
    private bool $permaDelete;

    public function __construct(array $postIds, bool $includeTrashed, bool $permaDelete = false)
    {
        $this->postIds = $postIds;
        $this->includeTrashed = $includeTrashed;
        $this->permaDelete = $permaDelete;
    }

    protected function transact()
    {
        $query = Post::whereIn('id', $this->postIds);

        if ($this->permaDelete && $this->includeTrashed) {
            $query = $query->withTrashed();
        }

        if ($query->count() == 0 || ($this->permaDelete && $query->notDeleted()->count() == 0)) {
            return null;
        }

        // Fetch the approved, non-deleted subset of the posts so we can operate on the affected
        // threads and categories below
        $posts = with(clone $query)->approved()->notDeleted()->with(['thread', 'thread.category'])->get();

        $this->permaDelete
            ? $query->forceDelete()
            : Post::withoutTimestamps(fn () => $query->delete());

        if ($posts->count() == 0) {
            // We only dealt with unapproved and/or soft-deleted posts, so no thread or category
            // update is necessary
            return $posts;
        }

        $threads = $posts->pluck('thread')->unique();
        $categories = $threads->pluck('category')->unique();
        foreach ($categories as $category) {
            $categoryThreadsRemoved = 0;
            $categoryPostsRemoved = 0;

            foreach ($threads->where('category_id', $category->id) as $thread) {
                $threadPostsRemoved = $posts->where('thread_id', $thread->id)->count();
                $categoryPostsRemoved += $threadPostsRemoved;

                if ($thread->posts()->count() == 0) {
                    // No non-deleted posts left in this thread

                    if (!$thread->trashed() && $thread->approved()) {
                        // Thread has not been soft-deleted and is approved;
                        // it should count towards threads removed for this category
                        $categoryThreadsRemoved++;
                    }

                    // If the thread doesn't even have any soft-deleted posts, we'll delete it
                    // permanently. Otherwise soft-delete it so as not to orphan the posts.
                    if ($thread->posts()->withTrashed()->count() == 0) {
                        $thread->forceDelete();
                    } else {
                        $thread->delete();
                    }
                } else {
                    Thread::withoutTimestamps(fn () => $thread->update([
                        'last_post_id' => $thread->getLastPost()->id,
                        'reply_count' => DB::raw("reply_count - {$threadPostsRemoved}"),
                    ]));

                    Post::withoutTimestamps(function () use ($thread) {
                        $thread->posts()->withTrashed()->each(function ($post, $i) {
                            $post->update(['sequence' => $i + 1]);
                        });
                    });
                }
            }

            $attributes = [
                'latest_active_thread' => $category->getLatestActiveThreadId(),
            ];

            if ($categoryThreadsRemoved > 0) {
                $attributes['thread_count'] = DB::raw("thread_count - {$categoryThreadsRemoved}");
            }

            if ($categoryPostsRemoved > 0) {
                $attributes['post_count'] = DB::raw("post_count - {$categoryPostsRemoved}");
            }

            Category::withoutTimestamps(fn () => $category->update($attributes));
        }

        return $posts;
    }
}
