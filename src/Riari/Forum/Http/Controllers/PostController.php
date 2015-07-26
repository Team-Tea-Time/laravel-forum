<?php namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Riari\Forum\Events\UserCreatingPost;
use Riari\Forum\Events\UserEditingPost;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;
use Riari\Forum\Repositories\Posts;
use Riari\Forum\Repositories\Threads;

class PostController extends BaseController
{
    /**
     * @var Threads
     */
    protected $threads;

    /**
     * @var Posts
     */
    protected $posts;

    /**
     * Create a post controller instance.
     *
     * @param  Threads  $threads
     * @param  Posts  $posts
     */
    public function __construct(Threads $threads, Posts $posts)
    {
        $this->threads = $threads;
        $this->posts = $posts;

        $rules = config('forum.preferences.validation');
        $this->rules = array_merge_recursive($rules['base'], $rules['post|put']['post']);
    }

    /**
     * GET: return a 'create post' (thread reply) view.
     *
     * @param  Category  $category
     * @param  string  $categoryAlias
     * @param  Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function create(Category $category, $categoryAlias, Thread $thread)
    {
        event(new UserCreatingPost($thread));

        return view('forum::post.create', compact('category', 'thread'));
    }

    /**
     * POST: validate and store a submitted post.
     *
     * @param  Category  $category
     * @param  string  $categoryAlias
     * @param  Thread  $thread
     * @param  string  $threadAlias
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Category $category, $categoryAlias, Thread $thread, $threadAlias, Request $request)
    {
        $this->validate($request, $this->rules);

        $post = [
            'thread_id' => $thread->id,
            'author_id' => auth()->user()->id,
            'content'   => $request->input('content')
        ];

        $post = $this->posts->create($post);
        $post->thread->touch();

        alert('success', trans('forum::general.reply_added'));

        return redirect($post->route);
    }

    /**
     * GET: return an 'edit post' view.
     *
     * @param  Category  $category
     * @param  string  $categoryAlias
     * @param  Thread  $thread
     * @param  string  $threadAlias
     * @param  Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category, $categoryAlias, Thread $thread, $threadAlias, Post $post)
    {
        event(new UserEditingPost($post));

        return view('forum::post.edit', compact('category', 'thread', 'post'));
    }

    /**
     * PATCH: update a submitted existing post.
     *
     * @param  Category  $category
     * @param  string  $categoryAlias
     * @param  Thread  $thread
     * @param  string  $threadAlias
     * @param  Post  $post
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Category $category, $categoryAlias, Thread $thread, $threadAlias, Post $post, Request $request)
    {
        $this->validate($request, $this->rules);

        $post = $this->posts->update($post->id, [
            'thread_id' => $thread->id,
            'author_id' => auth()->user()->id,
            'content'   => $request->input('content')
        ]);

        alert('success', trans('forum::posts.updated'));

        return redirect($post->route);
    }

    /**
     * DELETE: delete a post.
     *
     * @param  Category  $category
     * @param  string  $categoryAlias
     * @param  Thread  $thread
     * @param  string  $threadAlias
     * @param  Post  $post
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category, $categoryAlias, Thread $thread, $threadAlias, Post $post, Request $request)
    {
        $this->posts->delete($post->id);

        // Force deletion of the thread if it has no remaining posts
        if ($thread->posts->empty())
        {
            $this->threads->delete($thread->id);
            return redirect($category->route);
        }

        alert('success', trans('forum::posts.deleted'));

        return redirect($thread->route);
    }
}
