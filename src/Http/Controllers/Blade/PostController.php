<?php

namespace TeamTeaTime\Forum\Http\Controllers\Blade;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use TeamTeaTime\Forum\Events\UserCreatingPost;
use TeamTeaTime\Forum\Events\UserEditingPost;
use TeamTeaTime\Forum\Events\UserViewingPost;
use TeamTeaTime\Forum\Http\Requests\CreatePost;
use TeamTeaTime\Forum\Http\Requests\DeletePost;
use TeamTeaTime\Forum\Http\Requests\RestorePost;
use TeamTeaTime\Forum\Http\Requests\EditPost;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Support\Access\CategoryAccess;
use TeamTeaTime\Forum\Support\Frontend\Forum;

class PostController extends BaseController
{
    public function show(Request $request): View
    {
        $thread = $request->route('thread');
        $post = $request->route('post');

        if (!$post->isAccessibleTo($request->user())) {
            abort(404);
        }

        $post->load(['parent', 'parent.author', 'parent.thread', 'parent.thread.category']);

        if ($request->user() !== null) {
            UserViewingPost::dispatch($request->user(), $post);
        }

        return ViewFactory::make('forum::post.show', compact('thread', 'post'));
    }

    public function create(Request $request): View
    {
        $thread = $request->route('thread');

        $this->authorize('reply', $thread);

        UserCreatingPost::dispatch($request->user(), $thread);

        $post = $request->has('post_id') ? $thread->posts->find($request->input('post_id')) : null;

        if ($post !== null) {
            $post->load(['author', 'thread']);
        }

        return ViewFactory::make('forum::post.create', compact('thread', 'post'));
    }

    public function store(CreatePost $request): RedirectResponse
    {
        $thread = $request->route('thread');

        $this->authorize('reply', $thread);

        $post = $request->fulfill();

        Forum::alert('success', 'general.reply_added');

        return new RedirectResponse(Forum::route('thread.show', $post));
    }

    public function edit(Request $request): View
    {
        $post = $request->route('post');

        if ($post->trashed()) {
            return abort(404);
        }

        $this->authorize('edit', $post);
        
        $thread = $post->thread;
        $category = $post->thread->category;
        $post->load(['parent', 'parent.author', 'parent.thread', 'parent.thread.category']);

        UserEditingPost::dispatch($request->user(), $post);

        return ViewFactory::make('forum::post.edit', compact('category', 'thread', 'post'));
    }

    public function update(EditPost $request): RedirectResponse
    {
        $post = $request->route('post');

        $this->authorize('edit', $post);

        $post = $request->fulfill();

        Forum::alert('success', 'posts.updated');

        return new RedirectResponse(Forum::route('thread.show', $post));
    }

    public function confirmDelete(Request $request): View
    {
        $thread = $request->route('thread');
        $post = $request->route('post');
        $post->load(['parent', 'parent.author', 'parent.thread', 'parent.thread.category']);

        return ViewFactory::make('forum::post.confirm-delete', ['category' => $thread->category, 'thread' => $thread, 'post' => $post]);
    }

    public function confirmRestore(Request $request): View
    {
        $thread = $request->route('thread');
        $post = $request->route('post');
        $post->load(['parent', 'parent.author', 'parent.thread', 'parent.thread.category']);

        return ViewFactory::make('forum::post.confirm-restore', ['category' => $thread->category, 'thread' => $thread, 'post' => $post]);
    }

    public function delete(DeletePost $request): RedirectResponse
    {
        $post = $request->fulfill();

        if ($post === null) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'posts.deleted', 1);

        return new RedirectResponse(Forum::route('thread.show', $post->thread));
    }

    public function restore(RestorePost $request): RedirectResponse
    {
        $post = $request->fulfill();

        if ($post === null) {
            return $this->invalidSelectionResponse();
        }

        Forum::alert('success', 'posts.updated', 1);

        return new RedirectResponse(Forum::route('thread.show', $post));
    }

    public function pendingApproval(Request $request): View
    {
        $posts = Post::notDeleted()
            ->notFirstInThread()
            ->pendingApproval()
            ->orderBy('created_at', 'desc')
            ->with('thread', 'thread.category', 'author', 'parent', 'parent.thread', 'parent.thread.category');

        // Get accessible category IDs for the current user
        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($request->user());
        
        // Apply filtering to the query
        $posts = $posts->where(function ($query) use ($request, $accessibleCategoryIds) {
            // Public categories or private categories the user has access to
            $query->whereHas('thread.category', function ($q) use ($accessibleCategoryIds) {
                $q->where('is_private', false)
                  ->when($accessibleCategoryIds->isNotEmpty(), function ($q) use ($accessibleCategoryIds) {
                      $q->orWhereIn('id', $accessibleCategoryIds);
                  });
            });
            
            // Check view permissions for the thread
            if ($request->user()) {
                $query->whereHas('thread', function ($q) use ($accessibleCategoryIds) {
                    $q->where(function ($q) use ($accessibleCategoryIds) {
                        $q->whereDoesntHave('category', function ($q) {
                            $q->where('is_private', true);
                        })->orWhereHas('category', function ($q) use ($accessibleCategoryIds) {
                            $q->whereIn('id', $accessibleCategoryIds);
                        });
                    })->where(function ($q) use ($accessibleCategoryIds) {
                        $q->whereNull('category_id')
                          ->orWhereIn('category_id', $accessibleCategoryIds);
                    });
                });
            }
        });

        // Paginate the results
        $posts = $posts->paginate();

        return ViewFactory::make('forum::post.pending-approval', [
            'posts' => $posts
        ]);
    }
}
