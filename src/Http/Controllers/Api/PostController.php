<?php

namespace TeamTeaTime\Forum\Http\Controllers\Api;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use TeamTeaTime\Forum\Http\Resources\PostResource;
use TeamTeaTime\Forum\Models\Thread;

class PostController extends BaseController
{
    public function indexByThread(Thread $thread): AnonymousResourceCollection
    {
        if ($thread->category->is_private)
        {
            $this->authorize('view', $thread->category);
            $this->authorize('view', $thread);
        }

        return PostResource::collection($thread->posts()->paginate())
            ->additional([
                'links' => [
                    'thread' => route(config('forum.api.router.as') . 'thread.fetch', $thread->id)
                ]
            ]);
    }
}