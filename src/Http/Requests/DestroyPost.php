<?php

namespace TeamTeaTime\Forum\Http\Requests;

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

        if (config('forum.general.soft_deletes') && $this->isPermaDeleteRequested && is_callable([$post, 'forceDelete']))
        {
            $post->forceDelete();
        }
        else
        {
            $post->delete();
        }

        $post->thread->syncLastPost();
        $post->thread->category->syncLatestActiveThread();

        event(new UserDeletedPost($this->user(), $post));

        return $post;
    }
}
