<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use TeamTeaTime\Forum\{
    Actions\CreatePost as Action,
    Events\UserViewingThread,
    Http\Livewire\Traits\CreatesAlerts,
    Http\Livewire\Traits\UpdatesContent,
    Http\Livewire\EventfulPaginatedComponent,
    Models\Category,
    Models\Thread,
    Support\Validation\PostRules,
};

class ThreadShow extends EventfulPaginatedComponent
{
    use CreatesAlerts, UpdatesContent;

    public Thread $thread;

    // Form fields
    public string $content = '';

    public function mount(Request $request)
    {
        $this->thread = $request->route('thread');

        if (!$this->thread->category->isAccessibleTo($request->user())) {
            abort(404);
        }

        if ($request->user() !== null) {
            UserViewingThread::dispatch($request->user(), $this->thread);
            $this->thread->markAsRead($request->user());
        }
    }

    public function reply(Request $request)
    {
        if (!$request->user()->can('reply', $this->thread)) {
            abort(403);
        }

        $validated = $this->validate(PostRules::create());
        $parent = $request->has('post')
            ? $this->thread->posts->find($request->input('post'))
            : null;

        $action = new Action($this->thread, $parent, $request->user(), $validated['content']);
        $post = $action->execute();

        $post->thread->markAsRead($request->user());

        $this->content = '';

        $this->setPage($post->getPage());

        $this->touchUpdateKey();

        return $this->alert('general.reply_added')->toLivewire();
    }

    public function render(Request $request): View
    {
        $threadDestinationCategories = $request->user() && $request->user()->can('moveThreadsFrom', $this->thread->category)
            ? Category::acceptsThreads()->get()->toTree()
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