<?php

namespace TeamTeaTime\Forum\Actions;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;

class ApproveThread extends BaseAction
{
    private Thread $thread;

    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }

    protected function transact()
    {
        if ($this->thread->isApproved) {
            return null;
        }

        $this->thread->updateWithoutTouch([
            'approved_at' => Carbon::now(),
        ]);

        DB::table(Post::getTableName())->where('id', $this->thread->first_post_id)->update([
            'approved_at' => Carbon::now(),
        ]);

        $this->thread->category->updateWithoutTouch([
            'thread_count' => DB::raw('thread_count + 1'),
            'post_count' => DB::raw("post_count + {$this->thread->postCount}"),
            'newest_thread_id' => max($this->thread->id, $this->thread->category->newest_thread_id),
            'latest_active_thread_id' => $this->thread->category->getLatestActiveThreadId(),
        ]);

        return $this->thread;
    }
}
