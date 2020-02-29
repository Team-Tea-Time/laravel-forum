<?php namespace TeamTeaTime\Forum\Http\Controllers\Frontend;

use Forum;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use TeamTeaTime\Forum\Frontend\Events\UserCreatingThread;
use TeamTeaTime\Forum\Frontend\Events\UserMarkingNew;
use TeamTeaTime\Forum\Frontend\Events\UserViewingNew;
use TeamTeaTime\Forum\Frontend\Events\UserViewingThread;

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

        return view('forum::thread.index-new', compact('threads'));
    }

    /**
     * PATCH: Mark new/updated threads as read for the current user.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markNew(Request $request)
    {
        $threads = $this->api('thread.mark-new')->parameters($request->only('category_id'))->patch();

        event(new UserMarkingNew);

        if ($request->has('category_id')) {
            $category = $this->api('category.fetch', $request->input('category_id'))->get();

            if ($category) {
                Forum::alert('success', 'categories.marked_read', 0, ['category' => $category->title]);
                return redirect(Forum::route('category.show', $category));
            }
        }

        Forum::alert('success', 'threads.marked_read');
        return redirect(config('forum.routing.prefix'));
    }

    /**
     * GET: Return a thread view.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $thread = $this->api('thread.fetch', $request->route('thread'))
                       ->parameters(['include_deleted' => auth()->check()])
                       ->get();

        event(new UserViewingThread($thread));

        $category = $thread->category;

        $categories = [];
        if (Gate::allows('moveThreadsFrom', $category)) {
            $categories = $this->api('category.index')->parameters(['where' => ['category_id' => 0]], ['where' => ['enable_threads' => 1]])->get();
        }

        $posts = $thread->postsPaginated;

        return view('forum::thread.show', compact('categories', 'category', 'thread', 'posts'));
    }

    /**
     * GET: Return a 'create thread' view.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $category = $this->api('category.fetch', $request->route('category'))->get();

        if (!$category->threadsEnabled) {
            Forum::alert('warning', 'categories.threads_disabled');

            return redirect(Forum::route('category.show', $category));
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

        if (!$category->threadsEnabled) {
            Forum::alert('warning', 'categories.threads_disabled');

            return redirect(Forum::route('category.show', $category));
        }

        $thread = [
            'author_id'     => auth()->user()->getKey(),
            'category_id'   => $category->id,
            'title'         => $request->input('title'),
            'content'       => $request->input('content')
        ];

        $thread = $this->api('thread.store')->parameters($thread)->post();

        Forum::alert('success', 'threads.created');

        return redirect(Forum::route('thread.show', $thread));
    }

    /**
     * PATCH: Update a thread.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $action = $request->input('action');

        $thread = $this->api("thread.{$action}", $request->route('thread'))->parameters($request->all())->patch();

        Forum::alert('success', 'threads.updated', 1);

        return redirect(Forum::route('thread.show', $thread));
    }

    /**
     * DELETE: Delete a thread.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $this->validate($request, ['action' => 'in:delete,permadelete']);

        $permanent = !config('forum.preferences.soft_deletes') || ($request->input('action') == 'permadelete');

        $parameters = $request->all();
        $parameters['force'] = $permanent ? 1 : 0;

        $thread = $this->api('thread.delete', $request->route('thread'))->parameters($parameters)->delete();

        Forum::alert('success', 'threads.deleted', 1);

        return redirect($permanent ? Forum::route('category.show', $thread->category) : Forum::route('thread.show', $thread));
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

        $parameters['force'] = 0;
        if (!config('forum.preferences.soft_deletes') || ($request->input('action') == 'permadelete')) {
            $parameters['force'] = 1;
        }

        $threads = $this->api('bulk.thread.delete')->parameters($parameters)->delete();

        return $this->bulkActionResponse($threads, 'threads.deleted');
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

        return $this->bulkActionResponse($threads, 'threads.updated');
    }
}
