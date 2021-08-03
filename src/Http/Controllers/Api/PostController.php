<?php

namespace TeamTeaTime\Forum\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use TeamTeaTime\Forum\Http\Requests\CreatePost;
use TeamTeaTime\Forum\Http\Requests\DeletePost;
use TeamTeaTime\Forum\Http\Requests\RestorePost;
use TeamTeaTime\Forum\Http\Requests\SearchPosts;
use TeamTeaTime\Forum\Http\Requests\UpdatePost;
use TeamTeaTime\Forum\Http\Resources\PostResource;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;

class PostController extends BaseController
{
    public function indexByThread(Thread $thread): AnonymousResourceCollection
    {
        if ($thread->category->is_private) {
            $this->authorize('view', $thread->category);
            $this->authorize('view', $thread);
        }

        return PostResource::collection($thread->posts()->paginate());
    }

    public function search(SearchPosts $request): AnonymousResourceCollection
    {
        $posts = $request->fulfill();

        return PostResource::collection($posts);
    }

    public function recent(Request $request, bool $unreadOnly = false): AnonymousResourceCollection
    {
        $posts = Post::recent()
            ->get()
            ->filter(function (Post $post) use ($request, $unreadOnly) {
                return (! $unreadOnly || $post->thread->reader === null || $post->updatedSince($post->thread->reader))
                    && (
                        ! $post->thread->category->is_private
                        || $request->user()
                        && $request->user()->can('view', $post->thread->category)
                        && $request->user()->can('view', $post->thread)
                    );
            });

        return PostResource::collection($posts);
    }

    public function unread(Request $request): AnonymousResourceCollection
    {
        return $this->recent($request, true);
    }

    public function fetch(Post $post): PostResource
    {
        if ($post->thread->category->is_private) {
            $this->authorize('view', $post->thread->category);
            $this->authorize('view', $post->thread);
        }

        return new PostResource($post);
    }

    public function store(CreatePost $request): PostResource
    {
        $post = $request->fulfill();

        return new PostResource($post);
    }

    public function update(UpdatePost $request): PostResource
    {
        $post = $request->fulfill();

        return new PostResource($post);
    }

    public function delete(DeletePost $request): Response
    {
        $post = $request->fulfill();

        if ($post === null) {
            return $this->invalidSelectionResponse();
        }

        return new Response(new PostResource($post));
    }

    public function restore(RestorePost $request): Response
    {
        $post = $request->fulfill();

        if ($post === null) {
            return $this->invalidSelectionResponse();
        }

        return new Response(new PostResource($post));
    }
}
