<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Events\UserMarkedThreadsAsRead,
    Events\UserViewingUnapprovedThreads,
    Http\Livewire\Traits\CreatesAlerts,
    Http\Livewire\Traits\UpdatesContent,
    Models\Thread,
    Support\Access\CategoryAccess,
    Support\Access\ThreadAccess,
};

class UnapprovedThreads extends Component
{
    use CreatesAlerts, UpdatesContent;

    protected Collection $threads;

    protected function getThreads(Request $request): Collection
    {
        $threads = Thread::recent()->unapproved()->with('category', 'author', 'lastPost', 'lastPost.author', 'lastPost.thread');

        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($request->user());

        return $threads->get()->filter(function ($thread) use ($request, $accessibleCategoryIds) {
            return !$thread->category->is_private || $request->user() && $accessibleCategoryIds->contains($thread->category_id) && $request->user()->can('view', $thread);
        });
    }

    public function mount(Request $request)
    {
        $this->touchUpdateKey();
    }

    public function render(Request $request): View
    {
        $user = $request->user();
        $threads = $this->getThreads($request);

        UserViewingUnapprovedThreads::dispatch($request->user(), $threads);

        return ViewFactory::make('forum::pages.thread.unapproved', [
            'threads' => $threads,
        ])->layout('forum::layouts.main');
    }
}
