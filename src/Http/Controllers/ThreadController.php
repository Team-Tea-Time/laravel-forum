<?php

namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Riari\Forum\Events\UserCreatingThread;
use Riari\Forum\Events\UserMarkingThreadsRead;
use Riari\Forum\Events\UserViewingNew;
use Riari\Forum\Events\UserViewingThread;
use Riari\Forum\Forum;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;
use Riari\Forum\Routing\Dispatcher;

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
     * @param  Dispatcher  $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        parent::__construct($dispatcher);

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
        $threads = $this->api('thread.new.index')->get();

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

            $threads = $this->api('thread.new.mark')->patch();

            Forum::alert('success', 'threads', 'marked_read');
        }

        return redirect(config('forum.routing.root'));
    }

    /**
     * GET: return a thread view.
     *
     * @param  int  $categoryID
     * @param  string  $categorySlug
     * @param  int  $threadID
     * @return \Illuminate\Http\Response
     */
    public function show($categoryID, $categorySlug, $threadID)
    {
        $thread = $this->api('thread.show', $threadID)
                       ->parameters(['include_deleted' => true])
                       ->get();

        event(new UserViewingThread($thread));

        $posts = config('forum.preferences.list_trashed_posts') ? $thread->postsWithTrashedPaginated : $thread->postsPaginated;

        $category = $thread->category;

        return view('forum::thread.show', compact('category', 'thread', 'posts'));
    }

    /**
     * GET: return a 'create thread' view.
     *
     * @param  int  $categoryID
     * @return \Illuminate\Http\Response
     */
    public function create($categoryID)
    {
        $category = $this->api('category.show', $categoryID)->get();

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
     * @param  int  $categoryID
     * @param  string  $categorySlug
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($categoryID, $categorySlug, Request $request)
    {
        $category = $this->api('category.show', $categoryID)->get();

        if (!$category->threadsAllowed) {
            Forum::alert('warning', trans('forum::categories.threads_disallowed'));

            return redirect($category->route);
        }

        $this->validate($request, $this->rules['thread']);
        $this->validate($request, $this->rules['post']);

        $thread = [
            'author_id'     => auth()->user()->id,
            'category_id'   => $category->id,
            'title'         => $request->input('title'),
            'content'       => $request->input('content')
        ];
        $thread = $this->api('thread.store')->parameters($thread)->post();

        Forum::alert('success', 'threads', 'created');

        return redirect($thread->route);
    }
}
