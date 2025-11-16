<?php

namespace TeamTeaTime\Forum\Http\Controllers\Blade;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use TeamTeaTime\Forum\Events\UserCreatingThread;
use TeamTeaTime\Forum\Events\UserViewingRecent;
use TeamTeaTime\Forum\Events\UserViewingThread;
use TeamTeaTime\Forum\Events\UserViewingUnread;
use TeamTeaTime\Forum\Http\Requests\ApproveThread;
use TeamTeaTime\Forum\Http\Requests\CreateThread;
use TeamTeaTime\Forum\Http\Requests\DeleteThread;
use TeamTeaTime\Forum\Http\Requests\LockThread;
use TeamTeaTime\Forum\Http\Requests\MarkThreadsAsRead;
use TeamTeaTime\Forum\Http\Requests\MoveThread;
use TeamTeaTime\Forum\Http\Requests\PinThread;
use TeamTeaTime\Forum\Http\Requests\RenameThread;
use TeamTeaTime\Forum\Http\Requests\RestoreThread;
use TeamTeaTime\Forum\Http\Requests\UnapproveThread;
use TeamTeaTime\Forum\Http\Requests\UnlockThread;
use TeamTeaTime\Forum\Http\Requests\UnpinThread;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Support\Access\CategoryAccess;
use TeamTeaTime\Forum\Support\Frontend\Forum;

class ThreadController extends BaseController
{
    public function recent(Request $request): View
    {
        $threads = Thread::recent()->with('category', 'author', 'lastPost', 'lastPost.author', 'lastPost.thread');

        if ($request->has('category_id')) {
            $threads = $threads->where('category_id', $request->input('category_id'));
        }

        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($request->user);

        $threads = $threads->get()->filter(function ($thread) use ($request, $accessibleCategoryIds) {
            return $accessibleCategoryIds->contains($thread->category_id) && (!$thread->category->is_private || $request->user() && $request->user()->can('view', $thread));
        });

        if ($request->user() !== null) {
            UserViewingRecent::dispatch($request->user(), $threads);
        }

        return ViewFactory::make('forum::thread.recent', compact('threads'));
    }

    public function unread(Request $request): View
    {
        $threads = Thread::recent()->with('category', 'author', 'lastPost', 'lastPost.author', 'lastPost.thread');

        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($request->user());

        $threads = $threads->get()->filter(function ($thread) use ($request, $accessibleCategoryIds) {
            return $thread->userReadStatus !== null
                && (!$thread->category->is_private || $request->user() && $accessibleCategoryIds->contains($thread->category_id) && $request->user()->can('view', $thread));
        });

        if ($request->user() !== null) {
            UserViewingUnread::dispatch($request->user(), $threads);
        }

        return ViewFactory::make('forum::thread.unread', compact('threads'));
    }

    public function markAsRead(MarkThreadsAsRead $request): RedirectResponse
    {
        $category = $request->fulfill();

        if ($category !== null) {
            Forum::alert('success', 'categories.marked_read', 1, ['category' => $category->title]);

            return new RedirectResponse(Forum::route('category.show', $category));
        }

        Forum::alert('success', 'threads.marked_read');

        return new RedirectResponse(Forum::route('unread'));
    }

    public function show(Request $request): View
    {
        $thread = $request->route('thread');
        $user = $request->user();

        if (!$thread->isAccessibleTo($user)) {
            abort(404);
        }

        if ($user !== null) {
            UserViewingThread::dispatch($user, $thread);
            $thread->markAsRead($request->user());
        }

        $category = $thread->category;
        $categories = $user && $user->can('moveThreadsFrom', $category)
                    ? Category::acceptsThreads()->get()->toTree()
                    : [];

        $postsQuery = config('forum.general.display_trashed_posts') || $user && $user->can('viewTrashedPosts')
            ? $thread->posts()->withTrashed()
            : $thread->posts();

        if (!$user || !$user->can('approvePosts', $thread)) {
            $postsQuery = $postsQuery->approved();
        }

        if ($user) {
            $postsQuery = $postsQuery->orWhere(function ($query) use ($thread, $user)
                {
                    $query->where('thread_id', $thread->id)
                          ->whereNull('approved_at')
                          ->where('author_id', $user->getKey());
                });
        }

        $posts = $postsQuery
            ->with('author', 'thread')
            ->orderBy('created_at', 'asc')
            ->paginate();

        $selectablePostIds = [];
        if ($user) {
            foreach ($posts as $post) {
                $isReply = $post->sequence > 1;
                $canDeleteOrRestore = $user->can('delete', $post) || $user->can('restore', $post);
                $canApprove = ($post->approved_at == null || $post->approved_at > Carbon::now()) && $user->can('approvePosts', $thread);
                if ($isReply && ($canDeleteOrRestore || $canApprove)) {
                    $selectablePostIds[] = $post->id;
                }
            }
        }

        return ViewFactory::make('forum::thread.show', [
            'categories' => $categories,
            'category' => $category,
            'thread' => $thread,
            'posts' => $posts,
            'selectablePosts' => $selectablePostIds
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $category = $request->route('category');

        if (!$category->accepts_threads) {
            Forum::alert('warning', 'categories.threads_disabled');

            return new RedirectResponse(Forum::route('category.show', $category));
        }

        if ($request->user() !== null) {
            UserCreatingThread::dispatch($request->user(), $category);
        }

        return ViewFactory::make('forum::thread.create', compact('category'));
    }

    public function store(CreateThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        Forum::alert('success', 'threads.created');

        return new RedirectResponse(Forum::route('thread.show', $thread));
    }

    public function lock(LockThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        if ($thread === null) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'threads.updated');

        return new RedirectResponse(Forum::route('thread.show', $thread));
    }

    public function unlock(UnlockThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        if ($thread === null) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'threads.updated');

        return new RedirectResponse(Forum::route('thread.show', $thread));
    }

    public function pin(PinThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        if ($thread === null) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'threads.updated');

        return new RedirectResponse(Forum::route('thread.show', $thread));
    }

    public function unpin(UnpinThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        if ($thread === null) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'threads.updated');

        return new RedirectResponse(Forum::route('thread.show', $thread));
    }

    public function rename(RenameThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        Forum::alert('success', 'threads.updated');

        return new RedirectResponse(Forum::route('thread.show', $thread));
    }

    public function move(MoveThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        if ($thread === null) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'threads.updated');

        return new RedirectResponse(Forum::route('thread.show', $thread));
    }

    public function delete(DeleteThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        if ($thread === null) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'threads.deleted');

        return new RedirectResponse(Forum::route('category.show', $thread->category));
    }

    public function restore(RestoreThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        if ($thread === null) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'threads.updated');

        return new RedirectResponse(Forum::route('thread.show', $thread));
    }

    public function approve(ApproveThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        if ($thread === null) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'threads.approved');

        return new RedirectResponse(Forum::route('thread.show', $thread));
    }

    public function unapprove(UnapproveThread $request): RedirectResponse
    {
        $thread = $request->fulfill();

        if ($thread === null) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'threads.unapproved');

        return new RedirectResponse(Forum::route('thread.show', $thread));
    }

    public function pendingApproval(Request $request): View
    {
        $threads = Thread::notDeleted()->pendingApproval()->orderBy('created_at', 'desc')->with('category', 'author', 'lastPost', 'lastPost.author', 'lastPost.thread');

        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($request->user());

        $threads = $threads->get()->filter(function ($thread) use ($request, $accessibleCategoryIds) {
            return !$thread->category->is_private || $request->user() && $accessibleCategoryIds->contains($thread->category_id) && $request->user()->can('view', $thread);
        });

        return ViewFactory::make('forum::thread.pending-approval', compact('threads'));
    }
}
