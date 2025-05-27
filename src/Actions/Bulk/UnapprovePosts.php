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

class UnapprovePosts extends BaseAction
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
            ->approved()
            ->get();

        if ($posts->count() == 0) {
            return null;
        }

        // We only want to execute the action on the valid subset of the selection
        $eligiblePostIds = $posts->pluck('id');

        // Use the raw query builder to prevent touching updated_at
        $query = DB::table(Post::getTableName())->whereIn('id', $eligiblePostIds);
        $rowsAffected = $query->where('approved_at', '<=', Carbon::now()->toDateTimeString())
            ->update(['approved_at' => null]);

        if ($rowsAffected == 0) {
            return null;
        }

        $threadIds = $posts->pluck('thread_id')->unique()->values();
        $threads = Thread::whereIn('id', $threadIds)->approved()->get();
        foreach ($threads as $thread) {
            $lastApprovedPost = $thread->getLastApprovedPost();
            $postCount = $posts->where('thread_id', $thread->id)->count();

            $thread->updateWithoutTouch([
                'reply_count' => DB::raw("reply_count - {$postCount}"),
                'last_post_id' => $lastApprovedPost ? $lastApprovedPost->id : null,
                'updated_at' => $lastApprovedPost ? $lastApprovedPost->created_at : $thread->created_at
            ]);
        }

        $posts->load('thread');
        $categoryIds = $posts->pluck('thread.category_id')->unique()->values();
        $categories = Category::whereIn('id', $categoryIds)->get();
        foreach ($categories as $category) {
            $threadsInCategory = $threads->where('category_id', $category->id);
            $postCount = $posts->whereIn('thread_id', $threadsInCategory->pluck('id'))->count();

            $category->update([
                'post_count' => DB::raw("post_count - {$postCount}"),
                'latest_active_thread_id', $category->getLatestActiveThreadId()
            ]);
        }

        return $posts;
    }
}
