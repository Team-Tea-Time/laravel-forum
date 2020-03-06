<?php namespace TeamTeaTime\Forum\Http\Controllers\Frontend;

use Forum;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use TeamTeaTime\Forum\Events\UserCreatingThread;
use TeamTeaTime\Forum\Events\UserMarkingNew;
use TeamTeaTime\Forum\Events\UserViewingNew;
use TeamTeaTime\Forum\Events\UserViewingThread;
use TeamTeaTime\Forum\Http\Requests\StoreThread;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class ThreadController extends BaseController
{
    public function indexNew(): View
    {
        $threads = $this->api('thread.index-new')->get();

        event(new UserViewingNew($threads));

        return view('forum::thread.index-new', compact('threads'));
    }

    public function markNew(Request $request): RedirectResponse
    {
        $threads = $this->api('thread.mark-new')->parameters($request->only('category_id'))->patch();

        event(new UserMarkingNew);

        if ($request->has('category_id'))
        {
            $category = $this->api('category.fetch', $request->input('category_id'))->get();

            if ($category)
            {
                Forum::alert('success', 'categories.marked_read', 0, ['category' => $category->title]);
                return redirect(Forum::route('category.show', $category));
            }
        }

        Forum::alert('success', 'threads.marked_read');
        return redirect(config('forum.routing.prefix'));
    }

    public function show(Request $request, Thread $thread): View
    {
        event(new UserViewingThread($thread));

        $category = $thread->category;

        $categories = $request->user()->can('moveThreadsFrom', $category)
                    ? Category::acceptsThreads()->get()->toTree()
                    : [];

        $posts = $thread->postsPaginated;

        return view('forum::thread.show', compact('categories', 'category', 'thread', 'posts'));
    }

    public function create(Request $request, Category $category)
    {
        if (! $category->accepts_threads)
        {
            Forum::alert('warning', 'categories.threads_disabled');

            return redirect(Forum::route('category.show', $category));
        }

        event(new UserCreatingThread($category));

        return view('forum::thread.create', compact('category'));
    }

    public function store(StoreThread $request, Category $category)
    {
        if (! $category->accepts_threads)
        {
            Forum::alert('warning', 'categories.threads_disabled');

            return redirect(Forum::route('category.show', $category));
        }
        
        $thread = $request->fulfill();

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
