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

class RestorePosts extends BaseAction
{
    private array $postIds;

    public function __construct(array $postIds)
    {
        $this->postIds = $postIds;
    }

    protected function transact()
    {
        $query = Post::whereIn('id', $this->postIds)->onlyTrashed();

        if ($query->count() == 0) {
            return null;
        }

        // Fetch the approved subset of the psots so we can oeprate on the affected threads and
        // categories below
        $posts = with(clone $query)->approved()->with(['thread', 'thread.category'])->get();

        Post::withoutTimestamps(fn () => $query->restore());

        $threads = $posts->pluck('thread')->unique();
        $postsByThread = $posts->groupBy('thread_id');
        foreach ($threads as $thread) {
            $threadPosts = $postsByThread->get($thread->id);
            $lastApprovedPost = $thread->getLastApprovedPost();

            Thread::withoutTimestamps(fn () => $thread->update([
                'updated_at' => $lastApprovedPost ? $lastApprovedPost->created_at : $thread->created_at,
                'last_post_id' => $lastApprovedPost && $lastApprovedPost->sequence > 1 ? $lastApprovedPost->id : null,
                'reply_count' => DB::raw("reply_count + {$threadPosts->count()}"),
            ]));
        }

        $categories = $threads->pluck('category')->unique();
        $threadsByCategory = $threads->groupBy('category_id');

        foreach ($categories as $category) {
            $categoryThreads = $threadsByCategory->get($category->id);
            $postCount = $posts->whereNotNull('approved_at')
                ->where('approved_at', '<=', Carbon::now())
                ->whereIn('thread_id', $categoryThreads->pluck('id'))
                ->count();

            Category::withoutTimestamps(fn () => $category->update([
                'latest_active_thread_id' => $category->getLatestActiveThreadId(),
                'post_count' => DB::raw("post_count + {$postCount}"),
            ]));
        }

        return $posts;
    }
}
