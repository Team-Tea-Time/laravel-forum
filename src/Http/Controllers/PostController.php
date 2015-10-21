<?php

namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Riari\Forum\Events\UserCreatingPost;
use Riari\Forum\Events\UserEditingPost;
use Riari\Forum\Events\UserViewingPost;
use Riari\Forum\Forum;
use Riari\Forum\Http\Requests\CreatePostRequest;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;

class PostController extends BaseController
{
    /**
     * GET: Return a post view.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $post = $this->api('post.fetch', $request->route('post'))->parameters(['with' => ['thread', 'thread.category', 'parent']])->get();

        event(new UserViewingPost($post));

        $thread = $post->thread;
        $category = $thread->category;

        return view('forum::post.show', compact('category', 'thread', 'post'));
    }

    /**
     * GET: Return a 'create post' (thread reply) view.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $thread = $this->api('thread.fetch', $request->route('thread'))->parameters($request->only('post_id') + ['with' => ['posts']])->get();

        $this->authorize('reply', $thread);

        event(new UserCreatingPost($thread));

        $post = null;
        if ($request->has('post_id')) {
            $post = $thread->posts->find($request->input('post_id'));
        }

        return view('forum::post.create', compact('thread', 'post'));
    }

    /**
     * POST: Create a post.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $thread = $this->api('thread.fetch', $request->route('thread'))->parameters(['with' => ['posts']])->get();

        $this->authorize('reply', $thread);

        $post = null;
        if ($request->has('post_id')) {
            $post = $thread->posts->find($request->input('post_id'));
        }

        $post = $this->api('post.store')->parameters([
            'thread_id' => $thread->id,
            'author_id' => auth()->user()->id,
            'post_id'   => (is_null($post)) ? 0 : $post->id,
            'content'   => $request->input('content')
        ])->post();

        $post->thread->touch();

        Forum::alert('success', 'general', 'reply_added');

        return redirect($post->url);
    }

    /**
     * GET: Return an 'edit post' view.
     *
     * @param  int  $categoryID
     * @param  string  $categorySlug
     * @param  int  $threadID
     * @param  string  $threadSlug
     * @param  int  $postID
     * @return \Illuminate\Http\Response
     */
    public function edit($categoryID, $categorySlug, $threadID, $threadSlug, $postID)
    {
        $post = $this->api('post.fetch', $postID)->get();

        event(new UserEditingPost($post));

        if ($post->trashed()) {
            return abort(404);
        }

        $this->authorize($post);

        $thread = $post->thread;

        return view('forum::post.edit', compact('thread', 'post'));
    }

    /**
     * PATCH: Update an existing post.
     *
     * @param  int  $postID
     * @param  CreatePostRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($postID, CreatePostRequest $request)
    {
        $post = $this->api('post.fetch', $postID)->get();

        $this->authorize('edit', $post);

        $post = $this->api('post.update', $postID)->parameters($request->only('content'))->patch();

        Forum::alert('success', 'posts', 'updated');

        return redirect($post->url);
    }
}
