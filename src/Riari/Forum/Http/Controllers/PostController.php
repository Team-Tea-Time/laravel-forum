<?php namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Riari\Forum\Events\UserCreatingPost;
use Riari\Forum\Events\UserEditingPost;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;

class PostController extends BaseController
{
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
        $this->validate($request, config('forum.preferences.validation.post'));

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
        $this->validate($request, config('forum.preferences.validation.post'));

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
