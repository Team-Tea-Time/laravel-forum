<?php

namespace TeamTeaTime\Forum\Actions;

use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Models\{
    Category,
    Post,
    Thread,
};

class DeletePost extends BaseAction
{
    private Post $post;
    private bool $permaDelete;

    public function __construct(Post $post, bool $permaDelete = false)
    {
        $this->post = $post;
        $this->permaDelete = $permaDelete;
    }

    protected function transact()
    {
        if ($this->permaDelete) {
            $this->post->forceDelete();
        } else {
            if ($this->post->trashed()) {
                return null;
            }

            Post::withoutTimestamps(fn () => $this->post->delete());
        }

        $lastPostInThread = $this->post->thread->getLastPost();

        Thread::withoutTimestamps(fn () => $this->post->thread->update([
            'last_post_id' => $lastPostInThread->id,
            'updated_at' => $lastPostInThread->updated_at,
            'reply_count' => DB::raw('reply_count - 1'),
        ]));

        Category::withoutTimestamps(fn () => $this->post->thread->category->update([
            'latest_active_thread_id' => $this->post->thread->category->getLatestActiveThreadId(),
            'post_count' => DB::raw('post_count - 1'),
        ]));

        if ($this->permaDelete && $this->post->children !== null) {
            // Other posts reference this one; null their post IDs
            Post::withoutTimestamps(fn () => $this->post->children()->update(['post_id' => null]));
        }

        // Update sequence numbers for all of the thread's posts
        Post::withoutTimestamps(fn () => $this->post->thread->posts()->withTrashed()->each(function ($post, $i) {
            $post->update(['sequence' => $i + 1]);
        }));

        return $this->post;
    }
}
