<?php

namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Riari\Forum\Forum;
use Riari\Forum\Events\UserCreatingPost;
use Riari\Forum\Events\UserEditingPost;
use Riari\Forum\Events\UserViewingPost;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;

class PostController extends BaseController
{
    /**
     * @var Thread
     */
    protected $threads;

    /**
     * @var Post
     */
    protected $posts;

    /**
     * Create a post controller instance.
     *
     * @param  Thread  $threads
     * @param  Post  $posts
     */
    public function __construct(Thread $threads, Post $posts)
    {
        $this->threads = $threads;
        $this->posts = $posts;

        $rules = config('forum.preferences.validation');
        $this->rules = array_merge_recursive($rules['base'], $rules['post|put']['post']);
    }

    /**
     * GET: return a post view.
     *
     * @param  Category  $category
     * @param  string  $categorySlug
     * @param  Thread  $thread
     * @param  string  $threadSlug
     * @param  Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category, $categorySlug, Thread $thread, $threadSlug, Post $post)
    {
        event(new UserViewingPost($post));

        $this->authorize($post);

        return view('forum::post.show', compact('category', 'thread', 'post'));
    }

    /**
     * GET: return a 'create post' (thread reply) view.
     *
     * @param  Category  $category
     * @param  string  $categorySlug
     * @param  Thread  $thread
     * @param  string  $threadSlug
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Category $category, $categorySlug, Thread $thread, $threadSlug, Request $request)
    {
        event(new UserCreatingPost($thread));

        $this->authorize('reply', $thread);

        $post = null;
        if ($request->has('post_id')) {
            $post = $this->posts->where(['thread_id' => $thread->id, 'id' => $request->input('post_id')])->first();
        }

        return view('forum::post.create', compact('category', 'thread', 'post'));
    }

    /**
     * POST: validate and store a submitted post.
     *
     * @param  Category  $category
     * @param  string  $categorySlug
     * @param  Thread  $thread
     * @param  string  $threadSlug
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Category $category, $categorySlug, Thread $thread, $threadSlug, Request $request)
    {
        $this->validate($request, $this->rules);

        $this->authorize('reply', $thread);

        $post = null;
        if ($request->has('post_id')) {
            $post = $this->posts->where(['thread_id' => $thread->id, 'id' => $request->input('post_id')])->first();
        }

        $post = $this->posts->create([
            'thread_id' => $thread->id,
            'author_id' => auth()->user()->id,
            'post_id'   => (is_null($post)) ? 0 : $post->id,
            'content'   => $request->input('content')
        ]);
        $post->thread->touch();

        Forum::alert('success', trans('forum::general.reply_added'));

        return redirect($post->url);
    }

    /**
     * GET: return an 'edit post' view.
     *
     * @param  Category  $category
     * @param  string  $categorySlug
     * @param  Thread  $thread
     * @param  string  $threadSlug
     * @param  Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category, $categorySlug, Thread $thread, $threadSlug, Post $post)
    {
        event(new UserEditingPost($post));

        if ($post->trashed()) {
            return abort(404);
        }

        $this->authorize('update', $post);

        return view('forum::post.edit', compact('category', 'thread', 'post'));
    }

    /**
     * PATCH: update a submitted existing post.
     *
     * @param  Category  $category
     * @param  string  $categorySlug
     * @param  Thread  $thread
     * @param  string  $threadSlug
     * @param  Post  $post
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Category $category, $categorySlug, Thread $thread, $threadSlug, Post $post, Request $request)
    {
        $this->validate($request, $this->rules);

        $this->authorize($post);

        $this->posts->where('id', $post->id)->update([
            'content' => $request->input('content')
        ]);

        Forum::alert('success', trans('forum::posts.updated'));

        return redirect($post->fresh()->url);
    }
}
