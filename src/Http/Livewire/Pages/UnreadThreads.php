<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Events\UserViewingUnread,
    Http\Livewire\Traits\CreatesAlerts,
    Http\Livewire\Traits\UpdatesContent,
    Models\Thread,
    Support\Access\CategoryAccess,
};

class UnreadThreads extends Component
{
    use CreatesAlerts, UpdatesContent;

    protected Collection $threads;

    protected function getThreads(Request $request): Collection
    {
        $threads = Thread::recent()->with('category', 'author', 'lastPost', 'lastPost.author', 'lastPost.thread');

        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($request->user());

        return $threads->get()->filter(function ($thread) use ($request, $accessibleCategoryIds) {
            return $thread->userReadStatus !== null
                && (!$thread->category->is_private || $request->user() && $accessibleCategoryIds->contains($thread->category_id) && $request->user()->can('view', $thread));
        });
    }

    public function mount(Request $request)
    {
        $this->touchUpdateKey();
    }

    public function markAsRead(Request $request): array
    {
        $threads = $this->getThreads($request);
        $threads->each(function ($thread) use ($request) {
            $thread->markAsRead($request->user());
        });

        $this->touchUpdateKey();

        return $this->alert('threads.marked_read')->toLivewire();
    }

    public function render(Request $request): View
    {
        $threads = $this->getThreads($request);

        if ($request->user() !== null) {
            UserViewingUnread::dispatch($request->user(), $threads);
        }

        return ViewFactory::make('forum::pages.thread.unread', [
            'threads' => $threads,
        ])->layout('forum::layouts.main');
    }
}
