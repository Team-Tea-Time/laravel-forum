<?php namespace Riari\Forum\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Thread;

class ThreadController extends BaseController
{
    /**
     * GET: show a thread.
     *
     * @param  Category  $category
     * @param  string  $categoryAlias
     * @param  Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category, $categoryAlias, Thread $thread)
    {
        return view('forum::thread.show', compact('category', 'thread'));
    }

    /**
     * GET: return a 'create post' (thread reply) view.
     *
     * @param  Category  $category
     * @param  string  $categoryAlias
     * @param  Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function create(Category $category)
    {
        return view('forum::thread.create', compact('category'));
    }

    /**
     * POST: validate and store a submitted thread.
     *
     * @param  Category  $category
     * @param  string  $categoryAlias
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Category $category, $categoryAlias, Request $request)
    {
        $this->validate($request, config('forum.preferences.validation.thread'));
        $this->validate($request, config('forum.preferences.validation.post'));

        $thread = [
            'author_id'     => Auth::user()->id,
            'category_id'   => $category->id,
            'title'         => $request->input('title')
        ];
        $thread = $this->threads->create($thread);

        $post = [
            'thread_id' => $thread->id,
            'author_id' => Auth::user()->id,
            'content'   => $request->input('content')
        ];
        $this->posts->create($post);

        alert('success', trans('forum::threads.created'));

        return redirect($thread->route);
    }
}
