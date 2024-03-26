<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use TeamTeaTime\Forum\Actions\CreatePost as Action;
use TeamTeaTime\Forum\Events\UserViewingThread;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Support\Frontend\Forum;
use TeamTeaTime\Forum\Support\Validation\PostRules;

class ThreadShow extends Component
{
    use WithPagination;

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

        Forum::alert('success', 'general.reply_added');

        $action = new Action($this->thread, $parent, $request->user(), $validated['content']);
        $post = $action->execute();

        return $this->redirect($post->route, true);
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
                if ($request->user()->can('delete', $post) || $request->user()->can('restore', $post)) {
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
