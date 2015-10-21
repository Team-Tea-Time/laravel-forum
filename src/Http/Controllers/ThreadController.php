<?php

namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Riari\Forum\Events\UserCreatingThread;
use Riari\Forum\Events\UserMarkingThreadsRead;
use Riari\Forum\Events\UserViewingNew;
use Riari\Forum\Events\UserViewingThread;
use Riari\Forum\Forum;
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
     * GET: Return a new/updated threads view.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexNew()
    {
        $threads = $this->api('thread.index-new')->get();

        event(new UserViewingNew($threads));

        return view('forum::thread.index-new', ['threads' => $threads]);
    }

    /**
     * PATCH: Mark new/updated threads as read for the current user.
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
     * GET: Return a thread view.
     *
     * @param  int  $categoryID
     * @param  string  $categorySlug
     * @param  int  $threadID
     * @return \Illuminate\Http\Response
     */
    public function show($categoryID, $categorySlug, $threadID)
    {
        $thread = $this->api('thread.fetch', $threadID)
                       ->parameters(['include_deleted' => auth()->check()])
                       ->get();

        event(new UserViewingThread($thread));

        $category = $thread->category;

        $categories = [];
        if (Gate::allows('moveThreads', $category)) {
            $categories = $this->api('category.index')->parameters(['where' => ['category_id' => null]], ['where' => ['allows_threads' => 1]])->get();
        }

        return view('forum::thread.show', compact('categories', 'category', 'thread'));
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
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
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
     * PATCH: Update a thread.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        $action = $request->input('action');

        $thread = $this->api("thread.{$action}", $id)->parameters($request->all())->patch();

        Forum::alert('success', 'threads', 'updated', 1);

        return redirect($thread->route);
    }

    /**
     * DELETE: Delete a thread.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id, Request $request)
    {
        $this->validate($request, ['action' => 'in:delete,permadelete']);

        $permanent = !config('forum.preferences.soft_deletes') || ($request->input('action') == 'permadelete');

        $parameters = $request->all();

        if ($permanent) {
            $parameters += ['force' => 1];
        }

        $thread = $this->api('thread.delete', $id)->parameters($parameters)->delete();

        Forum::alert('success', 'threads', 'deleted', 1);

        return redirect($permanent ? $thread->category->route : $thread->route);
    }

    /**
     * DELETE: Delete threads in bulk.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkDestroy(Request $request)
    {
        $this->validate($request, ['action' => 'in:delete,permadelete']);

        $parameters = $request->all();

        if (!config('forum.preferences.soft_deletes') || ($request->input('action') == 'permadelete')) {
            $parameters += ['force' => 1];
        }

        $threads = $this->api('bulk.thread.delete')->parameters($parameters)->delete();

        return $this->bulkActionResponse($threads, 'deleted');
    }

    /**
     * PATCH: Update threads in bulk.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkUpdate(Request $request)
    {
        $this->validate($request, ['action' => 'in:restore,move,pin,unpin,lock,unlock']);

        $action = $request->input('action');

        $threads = $this->api("bulk.thread.{$action}")->parameters($request->all())->patch();

        return $this->bulkActionResponse($threads, 'updated');
    }

    /**
     * Helper: Bulk action response.
     *
     * @param  Collection  $threads
     * @param  string  $transKey
     * @return \Illuminate\Http\RedirectResponse
     */
    private function bulkActionResponse(Collection $threads, $transKey)
    {
        if ($threads->count()) {
            Forum::alert('success', 'threads', $transKey, $threads->count());
        } else {
            Forum::alert('warning', 'general', 'invalid_selection');
        }

        return redirect()->back();
    }
}
