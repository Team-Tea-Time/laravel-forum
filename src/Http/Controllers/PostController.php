<?php

namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Riari\Forum\Events\UserCreatingPost;
use Riari\Forum\Events\UserEditingPost;
use Riari\Forum\Events\UserViewingPost;
use Riari\Forum\Forum;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;
use Riari\Forum\Routing\Dispatcher;

class PostController extends BaseController
{
    /**
     * Create a post controller instance.
     *
     * @param  Dispatcher  $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        parent::__construct($dispatcher);

        $rules = config('forum.preferences.validation');
        $this->rules = array_merge_recursive($rules['base'], $rules['post|put']['post']);
    }

    /**
     * GET: return a post view.
     *
     * @param  int  $categoryID
     * @param  string  $categorySlug
     * @param  int  $threadID
     * @param  string  $threadSlug
     * @param  int  $postID
     * @return \Illuminate\Http\Response
     */
    public function show($categoryID, $categorySlug, $threadID, $threadSlug, $postID)
    {
        $post = $this->api('post.show', $postID)->parameters(['with' => ['thread', 'thread.category', 'parent']])->get();

        event(new UserViewingPost($post));

        return view('forum::post.show', compact('post'));
    }

    /**
     * GET: return a 'create post' (thread reply) view.
     *
     * @param  int  $categoryID
     * @param  string  $categorySlug
     * @param  int  $threadID
     * @param  string  $threadSlug
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create($categoryID, $categorySlug, $threadID, $threadSlug, Request $request)
    {
        $thread = $this->api('thread.show', $threadID)->parameters($request->only('post_id') + ['with' => ['posts']])->get();

        event(new UserCreatingPost($thread));

        $this->authorize('reply', $thread);

        $post = null;
        if ($request->has('post_id')) {
            $post = $thread->posts->find($request->input('post_id'));
        }

        return view('forum::post.create', compact('thread', 'post'));
    }

    /**
     * POST: validate and store a submitted post.
     *
     * @param  int  $categoryID
     * @param  string  $categorySlug
     * @param  int  $threadID
     * @param  string  $threadSlug
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($categoryID, $categorySlug, $threadID, $threadSlug, Request $request)
    {
        $this->validate($request, $this->rules);

        $thread = $this->api('thread.show', $threadID)->parameters(['with' => ['posts']])->get();

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
     * GET: return an 'edit post' view.
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
        $post = $this->api('post.show', $postID)->get();

        event(new UserEditingPost($post));

        if ($post->trashed()) {
            return abort(404);
        }

        $this->authorize($post);

        return view('forum::post.edit', compact('post'));
    }

    /**
     * PATCH: update a submitted existing post.
     *
     * @param  int  $categoryID
     * @param  string  $categorySlug
     * @param  int  $threadID
     * @param  string  $threadSlug
     * @param  int  $postID
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($categoryID, $categorySlug, $threadID, $threadSlug, $postID, Request $request)
    {
        $this->validate($request, $this->rules);

        $post = $this->api('post.show', $postID)->get();

        $this->authorize('edit', $post);

        $post = $this->api('post.update', $postID)->parameters($request->only('content'))->patch();

        Forum::alert('success', 'posts', 'updated');

        return redirect($post->url);
    }
}
