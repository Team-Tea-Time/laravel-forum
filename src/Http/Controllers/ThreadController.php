<?php

namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Riari\Forum\Forum;
use Riari\Forum\Events\UserCreatingThread;
use Riari\Forum\Events\UserMarkingThreadsRead;
use Riari\Forum\Events\UserViewingNew;
use Riari\Forum\Events\UserViewingThread;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;

class ThreadController extends BaseController
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
     * Create a thread controller instance.
     *
     * @param  Thread  $threads
     * @param  Post  $posts
     */
    public function __construct(Thread $threads, Post $posts)
    {
        $this->threads = $threads;
        $this->posts = $posts;

        $rules = config('forum.preferences.validation');
        $this->rules = [
            'thread'    => array_merge_recursive($rules['base'], $rules['post|put']['thread']),
            'post'      => array_merge_recursive($rules['base'], $rules['post|put']['post'])
        ];
    }

    /**
     * GET: return a new/updated threads view.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexNew()
    {
        $threads = $this->threads->newForReader;

        event(new UserViewingNew($threads));

        return view('forum::thread.index-new', ['threads' => $threads]);
    }

    /**
     * PATCH: mark new/updated threads as read for the current user.
     */
    public function markRead()
    {
        if (auth()->check()) {
            event(new UserMarkingThreadsRead);

            $threads = $this->threads->newForReader;

            foreach ($threads as $thread) {
                $thread->markAsRead(auth()->user()->id);
            }

            Forum::alert('success', trans('forum::threads.marked_read'));
        }

        return redirect(config('forum.routing.root'));
    }

    /**
     * GET: return a thread view.
     *
     * @param  Category  $category
     * @param  string  $categorySlug
     * @param  Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category, $categorySlug, Thread $thread)
    {
        event(new UserViewingThread($thread));

        return view('forum::thread.show', compact('category', 'thread'));
    }

    /**
     * GET: return a 'create thread' view.
     *
     * @param  Category  $category
     * @return \Illuminate\Http\Response
     */
    public function create(Category $category)
    {
        if (!$category->threadsAllowed) {
            Forum::alert('warning', trans('forum::categories.threads_disallowed'));

            return redirect($category->route);
        }

        event(new UserCreatingThread($category));

        return view('forum::thread.create', compact('category'));
    }

    /**
     * POST: validate and store a submitted thread.
     *
     * @param  Category  $category
     * @param  string  $categorySlug
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Category $category, $categorySlug, Request $request)
    {
        if (!$category->threadsAllowed) {
            Forum::alert('warning', trans('forum::categories.threads_disallowed'));

            return redirect($category->route);
        }

        $this->validate($request, $this->rules['thread']);
        $this->validate($request, $this->rules['post']);

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

        Forum::alert('success', trans('forum::threads.created'));

        return redirect($thread->route);
    }
}
