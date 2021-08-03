<?php

namespace TeamTeaTime\Forum\Http\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use TeamTeaTime\Forum\Events\UserCreatingThread;
use TeamTeaTime\Forum\Events\UserViewingRecent;
use TeamTeaTime\Forum\Events\UserViewingThread;
use TeamTeaTime\Forum\Events\UserViewingUnread;
use TeamTeaTime\Forum\Http\Requests\CreateThread;
use TeamTeaTime\Forum\Http\Requests\DeleteThread;
use TeamTeaTime\Forum\Http\Requests\LockThread;
use TeamTeaTime\Forum\Http\Requests\MarkThreadsAsRead;
use TeamTeaTime\Forum\Http\Requests\MoveThread;
use TeamTeaTime\Forum\Http\Requests\PinThread;
use TeamTeaTime\Forum\Http\Requests\RenameThread;
use TeamTeaTime\Forum\Http\Requests\RestoreThread;
use TeamTeaTime\Forum\Http\Requests\UnlockThread;
use TeamTeaTime\Forum\Http\Requests\UnpinThread;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Support\Web\Forum;

class ThreadController extends BaseController
{
    public function recent(Request $request): View
    {
        $threads = Thread::recent()->with('category', 'author', 'lastPost', 'lastPost.author');

        if ($request->has('category_id')) {
            $threads = $threads->where('category_id', $request->input('category_id'));
        }

        $threads = $threads->get()->filter(function ($thread) use ($request) {
            return ! $thread->category->private || $request->user() && $request->user()->can('view', $thread->category) && $request->user()->can('view', $thread);
        });

        UserViewingRecent::dispatch($request->user(), $threads);

        return view('forum::thread.recent', compact('threads'));
    }

    public function unread(Request $request): View
    {
        $threads = Thread::recent();

        $threads = $threads->get()->filter(function ($thread) use ($request) {
            return $thread->userReadStatus !== null
                && (! $thread->category->private || $request->user() && $request->user()->can('view', $thread->category) && $request->user()->can('view', $thread));
        });

        UserViewingUnread::dispatch($request->user(), $threads);

        return view('forum::thread.unread', compact('threads'));
    }

    public function markAsRead(MarkThreadsAsRead $request): RedirectResponse
    {
        $category = $request->fulfill();

        if ($category !== null) {
            Forum::alert('success', 'categories.marked_read', 1, ['category' => $category->title]);

            return redirect(Forum::route('category.show', $category));
        }

        Forum::alert('success', 'threads.marked_read');

        return redirect(Forum::route('unread'));
    }

    public function show(Request $request, Thread $thread): View
    {
        $this->authorize('view', $thread);

        UserViewingThread::dispatch($request->user(), $thread);

        $thread->markAsRead($request->user()->getKey());

        $category = $thread->category;
        $categories = $request->user() && $request->user()->can('moveThreadsFrom', $category)
                    ? Category::acceptsThreads()->get()->toTree()
                    : [];

        $posts = config('forum.general.display_trashed_posts') || $request->user() && $request->user()->can('viewTrashedPosts')
               ? $thread->posts()->withTrashed()
               : $thread->posts();

        $posts = $posts
            ->with('author', 'thread')
            ->orderBy('created_at', 'asc')
            ->paginate();

        return view('forum::thread.show', compact('categories', 'category', 'thread', 'posts'));
    }

    public function create(Request $request, Category $category): View
    {
        if (! $category->accepts_threads) {
            Forum::alert('warning', 'categories.threads_disabled');

            return redirect(Forum::route('category.show', $category));
        }

        UserCreatingThread::dispatch($request->user(), $category);

        return view('forum::thread.create', compact('category'));
    }

    public function store(CreateThread $request, Category $category): RedirectResponse
    {
        $thread = $request->fulfill();

        Forum::alert('success', 'threads.created');

        return redirect(Forum::route('thread.show', $thread));
    }

    public function lock(LockThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        Forum::alert('success', 'threads.updated');

        return redirect(Forum::route('thread.show', $thread));
    }

    public function unlock(UnlockThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        Forum::alert('success', 'threads.updated');

        return redirect(Forum::route('thread.show', $thread));
    }

    public function pin(PinThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        Forum::alert('success', 'threads.updated');

        return redirect(Forum::route('thread.show', $thread));
    }

    public function unpin(UnpinThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        Forum::alert('success', 'threads.updated');

        return redirect(Forum::route('thread.show', $thread));
    }

    public function rename(RenameThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        Forum::alert('success', 'threads.updated');

        return redirect(Forum::route('thread.show', $thread));
    }

    public function move(MoveThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        if (is_null($thread)) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'threads.updated');

        return redirect(Forum::route('thread.show', $thread));
    }

    public function delete(DeleteThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        if (is_null($thread)) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'threads.deleted');

        return redirect(Forum::route('category.show', $thread->category));
    }

    public function restore(RestoreThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        if (is_null($thread)) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'threads.updated');

        return redirect(Forum::route('thread.show', $thread));
    }
}
