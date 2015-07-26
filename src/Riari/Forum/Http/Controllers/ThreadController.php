<?php namespace Riari\Forum\Http\Controllers;

use Illuminate\Http\Request;
use Riari\Forum\Events\UserCreatingThread;
use Riari\Forum\Events\UserMarkedThreadsRead;
use Riari\Forum\Events\UserToggledLockThread;
use Riari\Forum\Events\UserToggledPinThread;
use Riari\Forum\Events\UserViewingNew;
use Riari\Forum\Events\UserViewingThread;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Thread;
use Riari\Forum\Repositories\Posts;
use Riari\Forum\Repositories\Threads;

class ThreadController extends BaseController
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
     * Create a thread controller instance.
     *
     * @param  Threads  $threads
     * @param  Posts  $posts
     */
    public function __construct(Threads $threads, Posts $posts)
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
        $threads = $this->threads->getNewForUser();

        event(new UserViewingNew($threads));

        return view('forum::thread.index-new', ['threads' => $threads]);
    }

    /**
     * PATCH: mark new/updated threads as read for the current user.
     */
    public function markRead()
    {
        if (auth()->check()) {
            $this->threads->markNewForUserAsRead();

            event(new UserMarkedThreadsRead);

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
        event(new UserCreatingThread($category));

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

        alert('success', trans('forum::threads.created'));

        return redirect($thread->route);
    }

    /**
     * PATCH: lock/unlock a thread.
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

        event(new UserToggledLockThread($thread));

        alert('success', trans('forum::threads.updated'));

        return redirect($thread->route);
    }

    /**
     * PATCH: pin/unpin a thread.
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

        event(new UserToggledPinThread($thread));

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
        foreach ($thread->posts as $post) {
            $this->posts->delete($post->id);
        }

        $this->threads->delete($thread->id);

        alert('success', trans('forum::threads.deleted'));

        return redirect($category->route);
    }
}
