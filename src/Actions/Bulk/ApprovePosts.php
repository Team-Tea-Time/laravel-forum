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

class ApprovePosts extends BaseAction
{
    private array $postIds;

    public function __construct(array $postIds)
    {
        $this->postIds = $postIds;
    }

    protected function transact()
    {
        $query = Post::whereIn('id', $this->postIds)
            ->notDeleted()
            ->notFirstInThread()
            ->pendingApproval();

        if ($query->count() == 0) {
            return null;
        }

        // Fetch the posts so we can operate on affected threads and categories below
        $posts = $query->with(['thread', 'thread.category'])->get();

        Post::withoutTimestamps(fn () => $query->update(['approved_at' => Carbon::now()->subSecond()]));

        $threads = $posts->pluck('thread')->unique()->values();
        foreach ($threads as $thread) {
            $lastApprovedPost = $thread->getLastApprovedPost();
            $postCount = $posts->where('thread_id', $thread->id)->count();

            Thread::withoutTimestamps(fn () => $thread->update([
                'reply_count' => DB::raw("reply_count + {$postCount}"),
                'last_post_id' => $lastApprovedPost ? $lastApprovedPost->id : null,
                'updated_at' => $lastApprovedPost ? $lastApprovedPost->created_at : $thread->created_at
            ]));
        }

        $categories = $threads->pluck('category')->unique()->values();
        foreach ($categories as $category) {
            $threadsInCategory = $threads->whereNotNull('approved_at')
                ->where('approved_at', '<=', Carbon::now())
                ->where('category_id', $category->id);

            if ($threadsInCategory->count() == 0) continue;

            $postCount = $posts->whereIn('thread_id', $threadsInCategory->pluck('id'))->count();

            $category->update([
                'post_count' => DB::raw("post_count + {$postCount}"),
                'newest_thread_id' => $category->getNewestThreadId(),
                'latest_active_thread_id' => $category->getLatestActiveThreadId()
            ]);
        }

        return $posts;
    }
}
