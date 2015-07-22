<?php namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Thread;

class ThreadController extends BaseController
{
    /**
     * GET: return a new/updated threads view.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexNew()
    {
        return view('forum::thread.index-new', ['threads' => $this->threads->getNewForUser()]);
    }

    /**
     * PATCH: mark new/updated threads as read for the current user.
     */
    public function markRead()
    {
        if (auth()->check()) {
            $this->threads->markNewForUserAsRead();
            alert('success', trans('forum::threads.marked_read'));
        }

        return redirect(config('forum.routing.root'));
    }

    /**
     * GET: return a thread view.
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
            'author_id'     => auth()->user()->id,
            'category_id'   => $category->id,
            'title'         => $request->input('title')
        ];
        $thread = $this->threads->create($thread);

        $post = [
            'thread_id' => $thread->id,
            'author_id' => auth()->user()->id,
            'content'   => $request->input('content')
        ];
        $this->posts->create($post);

        alert('success', trans('forum::threads.created'));

        return redirect($thread->route);
    }

    /**
     * PATCH: lock a thread.
     *
     * @param  Category  $category
     * @param  string  $categoryAlias
     * @param  Thread  $thread
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function lock(Category $category, $categoryAlias, Thread $thread, Request $request)
    {
        $thread->toggle('locked');

        alert('success', trans('forum::threads.updated'));

        return redirect($thread->route);
    }

    /**
     * PATCH: pin a thread.
     *
     * @param  Category  $category
     * @param  string  $categoryAlias
     * @param  Thread  $thread
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pin(Category $category, $categoryAlias, Thread $thread, Request $request)
    {
        $thread->toggle('pinned');

        alert('success', trans('forum::threads.updated'));

        return redirect($thread->route);
    }

    /**
     * DELETE: delete a thread (and its posts).
     *
     * @param  Category  $category
     * @param  string  $categoryAlias
     * @param  Thread  $thread
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category, $categoryAlias, Thread $thread, Request $request)
    {
        foreach ($thread->posts as $post)
        {
            $this->posts->delete($post->id);
        }

        $this->threads->delete($thread->id);

        alert('success', trans('forum::threads.deleted'));

        return redirect($category->route);
    }
}
