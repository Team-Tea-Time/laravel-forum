<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use TeamTeaTime\Forum\{
    Actions\Bulk\DeletePosts,
    Actions\Bulk\RestorePosts,
    Events\UserBulkDeletedPosts,
    Events\UserBulkRestoredPosts,
    Events\UserViewingThread,
    Http\Livewire\Forms\ThreadEditForm,
    Http\Livewire\Forms\ThreadReplyForm,
    Http\Livewire\Traits\CreatesAlerts,
    Http\Livewire\Traits\UpdatesContent,
    Http\Livewire\EventfulPaginatedComponent,
    Models\Category,
    Models\Thread,
    Support\Access\CategoryAccess,
    Support\Authorization\PostAuthorization,
    Support\Traits\HandlesDeletion,
};

class ThreadShow extends EventfulPaginatedComponent
{
    use CreatesAlerts, UpdatesContent, HandlesDeletion;

    public Thread $thread;

    public ThreadEditForm $threadEditForm;
    public int $destinationCategoryId = 0;

    public ThreadReplyForm $threadReplyForm;

    public function mount(Request $request)
    {
        $this->thread = $request->route('thread');
        $this->threadEditForm->title = $this->thread->title;
        $this->title = $this->thread->title;

        if (!$this->thread->isAccessibleTo($request->user())) {
            abort(404);
        }

        if ($request->user() !== null) {
            UserViewingThread::dispatch($request->user(), $this->thread);
            $this->thread->markAsRead($request->user());
        }
    }

    public function delete(Request $request, bool $permadelete)
    {
        $this->thread = $this->threadEditForm->delete($request, $this->thread, $permadelete);

        return $this->redirect($permadelete ? $this->thread->category->route : $this->thread->route);
    }

    public function restore(Request $request): array
    {
        $this->thread = $this->threadEditForm->restore($request, $this->thread);

        return $this->pluralAlert('threads.restored')->toLivewire();
    }

    public function lock(Request $request): array
    {
        $this->thread = $this->threadEditForm->lock($request, $this->thread);

        return $this->pluralAlert('threads.updated')->toLivewire();
    }

    public function unlock(Request $request): array
    {
        $this->thread = $this->threadEditForm->unlock($request, $this->thread);

        return $this->pluralAlert('threads.updated')->toLivewire();
    }

    public function pin(Request $request): array
    {
        $this->thread = $this->threadEditForm->pin($request, $this->thread);

        return $this->pluralAlert('threads.updated')->toLivewire();
    }

    public function unpin(Request $request): array
    {
        $this->thread = $this->threadEditForm->unpin($request, $this->thread);

        return $this->pluralAlert('threads.updated')->toLivewire();
    }

    public function rename(Request $request): array
    {
        $this->thread = $this->threadEditForm->rename($request, $this->thread);

        return $this->pluralAlert('threads.updated')->toLivewire();
    }

    public function move(Request $request): array
    {
        $destination = Category::find($this->destinationCategoryId);

        if ($destination == null) {
            return $this->invalidSelectionAlert()->toLivewire();
        }

        $this->threadEditForm->move($request, $this->thread, $destination);
        $this->thread->category = $destination;
        $this->destinationCategoryId = 0;

        return $this->pluralAlert('threads.updated')->toLivewire();
    }

    public function reply(Request $request): array
    {
        $post = $this->threadReplyForm->reply($request, $this->thread);

        $this->setPage($post->getPage());
        $this->touchUpdateKey();

        return $this->alert('general.reply_added')->toLivewire();
    }

    public function deletePosts(Request $request, array $postIds, bool $permadelete): array
    {
        if (!PostAuthorization::bulkDelete($request->user(), $postIds)) {
            abort(403);
        }

        $action = new DeletePosts($postIds, $request->user()->can('viewTrashedPosts'), $this->shouldPermaDelete($permadelete));
        $result = $action->execute();

        $this->touchUpdateKey();

        if ($result !== null) {
            UserBulkDeletedPosts::dispatch($request->user(), $result);
        }

        return $this->pluralAlert('posts.deleted', $result->count())->toLivewire();
    }

    public function restorePosts(Request $request, array $postIds): array
    {
        if (!PostAuthorization::bulkRestore($request->user(), $postIds)) {
            abort(403);
        }

        $action = new RestorePosts($postIds);
        $result = $action->execute();

        $this->touchUpdateKey();

        if ($result !== null) {
            UserBulkRestoredPosts::dispatch($request->user(), $result);
        }

        return $this->pluralAlert('posts.restored', $result->count())->toLivewire();
    }

    public function render(Request $request): View
    {
        $threadDestinationCategories = $request->user() && $request->user()->can('moveThreadsFrom', $this->thread->category)
            ? CategoryAccess::getFilteredTreeFor($request->user())->toTree()
            : [];

        $postsQuery = config('forum.general.display_trashed_posts') || $request->user() && $request->user()->can('viewTrashedPosts')
            ? $this->thread->posts()->withTrashed()
            : $this->thread->posts();

        $posts = $postsQuery
            ->with('author', 'thread')
            ->orderBy('created_at', 'asc')
            ->paginate();

        $selectablePostIds = [];
        if ($request->user()) {
            foreach ($posts as $post) {
                if ($post->sequence > 1 && ($request->user()->can('delete', $post) || $request->user()->can('restore', $post))) {
                    $selectablePostIds[] = $post->id;
                }
            }
        }

        return ViewFactory::make('forum::pages.thread.show', [
            'posts' => $posts,
            'threadDestinationCategories' => $threadDestinationCategories,
            'selectablePostIds' => $selectablePostIds,
        ])->layout('forum::layouts.main', ['category' => $this->thread->category, 'thread' => $this->thread]);
    }
}
