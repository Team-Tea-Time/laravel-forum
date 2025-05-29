<?php

namespace TeamTeaTime\Forum\Actions\Bulk;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Actions\BaseAction;
use TeamTeaTime\Forum\Models\Post;

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
        $posts = $query->approved()->notDeleted()->with(['thread', 'thread.category'])->get();

        if ($this->permaDelete) {
            $query->forceDelete();
        } else {
            Post::withoutTimestamps(fn () => $query->delete());
        }

        if ($posts->count() == 0) {
            // We only dealt with unapproved and/or soft-deleted posts, so no thread or category
            // update is necessary
            return $posts;
        }

        // TODO: Refactor below
        $threads = $posts->pluck('thread')->unique();
        $categories = $threads->pluck('category')->unique();
        foreach ($categories as $category) {
            $categoryThreadsRemoved = 0;
            $categoryPostsRemoved = 0;

            foreach ($threads->where('category_id', $category->id) as $thread) {
                $threadPostsRemoved = $posts->where('thread_id', $thread->id)
                    ->whereNull('deleted_at')
                    ->whereNotNull('approved_at')
                    ->where('approved_at', '<=', Carbon::now())
                    ->count();
                $categoryPostsRemoved += $threadPostsRemoved;

                // Skip updates if the affected posts were already soft-deleted
                // or there were no valid post IDs given for this thread
                if ($threadPostsRemoved == 0) {
                    continue;
                }

                if ($thread->posts()->count() == 0) {
                    if (!$thread->trashed() && $thread->approved()) {
                        // Thread has not been soft-deleted and is approved;
                        // it should count towards threads removed for this category
                        $categoryThreadsRemoved++;
                    }

                    if ($thread->posts()->withTrashed()->count() == 0) {
                        $thread->forceDelete();
                    } else {
                        $thread->delete();
                    }
                } else {
                    $thread->updateWithoutTouch([
                        'last_post_id' => $thread->getLastPost()->id,
                        'reply_count' => DB::raw("reply_count - {$threadPostsRemoved}"),
                    ]);

                    $thread->posts()->withTrashed()->each(function ($p, $i) {
                        $p->updateWithoutTouch(['sequence' => $i + 1]);
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

            $category->updateWithoutTouch($attributes);
        }

        return $posts;
    }
}
