<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Events\UserViewingUnapprovedPosts,
    Http\Livewire\Traits\CreatesAlerts,
    Http\Livewire\Traits\UpdatesContent,
    Models\Post,
    Support\Access\CategoryAccess,
};

class UnapprovedPosts extends Component
{
    use CreatesAlerts, UpdatesContent;

    protected Collection $threads;

    protected function getPosts(Request $request): Collection
    {
        $posts = Post::recent()->unapproved()->with('thread', 'author');

        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($request->user());

        return $posts->get()->filter(function ($post) use ($request, $accessibleCategoryIds) {
            return !$post->thread->category->is_private || $request->user() && $accessibleCategoryIds->contains($post->category_id) && $request->user()->can('view', $post->thread);
        });
    }

    public function mount(Request $request)
    {
        $this->touchUpdateKey();
    }

    public function render(Request $request): View
    {
        $posts = $this->getPosts($request);

        UserViewingUnapprovedPosts::dispatch($request->user(), $posts);

        return ViewFactory::make('forum::pages.post.unapproved', [
            'posts' => $posts,
        ])->layout('forum::layouts.main');
    }
}
