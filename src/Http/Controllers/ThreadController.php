<?php

namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Riari\Forum\Events\UserCreatingThread;
use Riari\Forum\Events\UserMarkingThreadsRead;
use Riari\Forum\Events\UserViewingNew;
use Riari\Forum\Events\UserViewingThread;
use Riari\Forum\Forum;
use Riari\Forum\Http\Requests\BulkUpdateThreadsRequest;
use Riari\Forum\Http\Requests\CreateThreadRequest;
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
            event(new UserMarkingNew);

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
        $thread = $this->api('thread.fetch', $threadID)
                       ->parameters(['include_deleted' => true])
                       ->get();

        event(new UserViewingThread($thread));

        $posts = config('forum.preferences.list_trashed_posts') ? $thread->postsWithTrashedPaginated : $thread->postsPaginated;

        $category = $thread->category;

        return view('forum::thread.show', compact('category', 'thread', 'posts'));
    }

    /**
     * GET: Return a 'create thread' view.
     *
     * @param  int  $categoryID
     * @return \Illuminate\Http\Response
     */
    public function create($categoryID)
    {
        $category = $this->api('category.fetch', $categoryID)->get();

        if (!$category->threadsAllowed) {
            Forum::alert('warning', trans('forum::categories.threads_disallowed'));

            return redirect($category->route);
        }

        event(new UserCreatingThread($category));

        return view('forum::thread.create', compact('category'));
    }

    /**
     * POST: Store a new thread.
     *
     * @param  CreateThreadRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateThreadRequest $request)
    {
        $category = $this->api('category.fetch', $request->route('category'))->get();

        if (!$category->threadsAllowed) {
            Forum::alert('warning', trans('forum::categories.threads_disallowed'));

            return redirect($category->route);
        }

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

    /**
     * DELETE|PATCH: Delete/update threads in bulk.
     *
     * @param  BulkUpdateThreadsRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkUpdate(BulkUpdateThreadsRequest $request)
    {
        $action = $request->input('action');
        $method = $request->method();

        $threads = $this->api("bulk.thread.{$action}")->parameters($request->all())->{$method}();

        $transKey = '';
        switch ($action) {
            case 'delete':
                $transKey = 'deleted';
                break;
            case 'move':
            case 'pin':
            case 'unpin':
            case 'lock':
            case 'unlock':
                $transKey = 'updated';
                break;
        }

        Forum::alert('success', 'threads', $transKey, $threads->count());

        return redirect()->back();
    }
}
