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
        $posts = Post::whereIn('id', $this->postIds)
            ->notDeleted()
            ->notFirstInThread()
            ->pendingApproval()
            ->get();

        if ($posts->count() == 0) {
            return null;
        }

        // We only want to execute the action on the valid subset of the selection
        $eligiblePostIds = $posts->pluck('id');

        // Use the raw query builder to prevent touching updated_at
        $query = DB::table(Post::getTableName())->whereIn('id', $eligiblePostIds);
        $rowsAffected = $query->whereNull('approved_at')
            ->orWhere('approved_at', '>', Carbon::now()->toDateTimeString())
            ->update(['approved_at' => DB::raw('now()')]);

        if ($rowsAffected == 0) {
            return null;
        }

        $posts->load('thread');

        $threadIds = $posts->pluck('thread_id')->unique()->values();
        $threads = Thread::whereIn('id', $threadIds)->get();
        foreach ($threads as $thread) {
            $lastApprovedPost = $thread->getLastApprovedPost();
            $thread->updateWithoutTouch([
                'reply_count' => $thread->posts()->approved()->count() - 1,
                'last_post_id' => $lastApprovedPost ? $lastApprovedPost->id : null,
                'updated_at' => $lastApprovedPost ? $lastApprovedPost->created_at : $thread->created_at
            ]);
        }

        $categoryIds = $posts->pluck('thread.category_id')->unique()->values();
        $categories = Category::whereIn('id', $categoryIds)->get();
        foreach ($categories as $category) {
            $threadsInCategory = $threads->whereNotNull('approved_at')
                ->where('approved_at', '<=', Carbon::now())
                ->where('category_id', $category->id);
            $postCount = $posts->whereIn('thread_id', $threadsInCategory->pluck('id'))->count();
            $category->update([
                'post_count' => DB::raw("post_count + {$postCount}"),
                'newest_thread_id' => $category->getNewestThreadid(),
                'latest_active_thread_id' => $category->getLatestActiveThreadId()
            ]);
        }

        return $posts;
    }
}
