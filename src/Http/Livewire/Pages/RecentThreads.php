<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Events\UserViewingRecent,
    Models\Thread,
    Support\Access\CategoryAccess,
};

class RecentThreads extends Component
{
    protected Collection $threads;

    public function mount(Request $request)
    {
        $threads = Thread::recent()->with('category', 'author', 'lastPost', 'lastPost.author', 'lastPost.thread');

        if ($request->has('category_id')) {
            $threads = $threads->where('category_id', $request->input('category_id'));
        }

        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($request->user());

        $this->threads = $threads->get()->filter(function ($thread) use ($request, $accessibleCategoryIds) {
            return $accessibleCategoryIds->contains($thread->category_id)
                && (!$thread->category->is_private || $request->user() && $request->user()->can('view', $thread));
        });

        if ($request->user() !== null) {
            UserViewingRecent::dispatch($request->user(), $this->threads);
        }
    }

    public function render(Request $request): View
    {
        return ViewFactory::make('forum::pages.thread.recent', [
            'threads' => $this->threads,
        ])->layout('forum::layouts.main');
    }
}
