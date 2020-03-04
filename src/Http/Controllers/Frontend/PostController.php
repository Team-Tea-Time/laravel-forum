<?php

namespace TeamTeaTime\Forum\Http\Controllers\Frontend;

use Forum;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use TeamTeaTime\Forum\Events\UserCreatingPost;
use TeamTeaTime\Forum\Events\UserEditingPost;
use TeamTeaTime\Forum\Events\UserViewingPost;
use TeamTeaTime\Forum\Http\Requests\BulkDestroyPosts;
use TeamTeaTime\Forum\Http\Requests\BulkUpdatePosts;
use TeamTeaTime\Forum\Http\Requests\StorePost;
use TeamTeaTime\Forum\Http\Requests\UpdatePost;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;

class PostController extends BaseController
{
    public function show(Request $request, Post $post): View
    {
        event(new UserViewingPost($post));

        $thread = $post->thread;
        $category = $thread->category;

        return view('forum::post.show', compact('category', 'thread', 'post'));
    }

    public function create(Request $request, Thread $thread): View
    {
        $this->authorize('reply', $thread);

        event(new UserCreatingPost($thread));

        $post = $request->has('post') ? $thread->posts->find($request->input('post')) : null;

        return view('forum::post.create', compact('thread', 'post'));
    }

    public function store(StorePost $request, Thread $thread): RedirectResponse
    {
        $this->authorize('reply', $thread);

        $post = $request->fulfill();

        Forum::alert('success', 'general.reply_added');

        return redirect(Forum::route('thread.show', $post));
    }

    public function edit(Request $request, Thread $thread, $threadSlug, Post $post): View
    {
        if ($post->trashed()) return abort(404);

        $this->authorize('edit', $post);

        event(new UserEditingPost($post));

        $thread = $post->thread;
        $category = $post->thread->category;

        return view('forum::post.edit', compact('category', 'thread', 'post'));
    }

    public function update(UpdatePost $request, Thread $thread, $threadSlug, Post $post): RedirectResponse
    {
        $this->authorize('edit', $post);

        $post = $request->fulfill();

        Forum::alert('success', 'posts.updated');

        return redirect(Forum::route('thread.show', $post));
    }

    public function destroy(DestroyPost $request): RedirectResponse
    {
        $post = $request->fulfill();

        Forum::alert('success', 'posts.deleted', 1);

        return redirect(Forum::route('thread.show', $post->thread));
    }

    public function bulkDestroy(BulkDestroyPosts $request): RedirectResponse
    {
        $posts = $request->fulfill();

        return $this->bulkActionResponse($posts, 'posts.deleted');
    }

    public function bulkUpdate(BulkUpdatePosts $request): RedirectResponse
    {
        $posts = $request->fulfill();

        return $this->bulkActionResponse($posts, 'posts.updated');
    }
}
