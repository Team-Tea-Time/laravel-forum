<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Events\UserDeletedPost;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\Post;

class DestroyPost extends BaseRequest implements FulfillableRequest
{
    public function authorize(): bool
    {
        $post = $this->route('post');
        return $post->sequence != 1 && $this->user()->can('delete', $post);
    }

    public function rules(): array
    {
        return [
            'permadelete' => ['boolean']
        ];
    }

    public function fulfill()
    {
        $post = $this->route('post');

        if ($this->isPermaDeleting())
        {
            $post->forceDelete();
        }
        else
        {
            $post->deleteWithoutTouch();
        }

        $lastPostInThread = $post->thread->getLastPost();

        $post->thread->updateWithoutTouch([
            'last_post_id' => $lastPostInThread->id,
            'updated_at' => $lastPostInThread->updated_at,
            'reply_count' => DB::raw('reply_count - 1')
        ]);

        $post->thread->category->updateWithoutTouch([
            'latest_active_thread_id' => $post->thread->category->getLatestActiveThreadId(),
            'post_count' => DB::raw('post_count - 1')
        ]);

        if (! is_null($post->children))
        {
            // Other posts reference this one; set their parent post IDs to 0
            $post->children()->update(['post_id' => 0]);
        }

        // Update sequence numbers for all of the thread's posts
        $post->thread->posts->each(function ($p)
        {
            $p->updateWithoutTouch(['sequence' => $p->getSequenceNumber()]);
        });

        event(new UserDeletedPost($this->user(), $post));

        return $post;
    }
}
