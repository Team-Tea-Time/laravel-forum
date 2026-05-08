<?php

namespace TeamTeaTime\Forum\Frontend\Stacks;

use TeamTeaTime\Forum\{
    Http\Livewire\Pages\CategoryCreate,
    Http\Livewire\Pages\CategoryEdit,
    Http\Livewire\Pages\CategoryIndex,
    Http\Livewire\Pages\CategoryShow,
    Http\Livewire\Pages\RecentThreads,
    Http\Livewire\Pages\UpdateCategoryTree,
    Http\Livewire\Pages\UnreadThreads,
    Http\Livewire\Pages\PostsPendingApproval,
    Http\Livewire\Pages\PostEdit,
    Http\Livewire\Pages\PostShow,
    Http\Livewire\Pages\ThreadsPendingApproval,
    Http\Livewire\Pages\ThreadCreate,
    Http\Livewire\Pages\ThreadReply,
    Http\Livewire\Pages\ThreadShow,
    Http\Middleware\ResolveFrontendParameters,
    Frontend\Traits\RegistersLivewireComponents,
};

class Livewire implements StackInterface
{
    use RegistersLivewireComponents;

    public function register(): void
    {
        $this->livewireComponent('forum.pages.category.index', CategoryIndex::class);
        $this->livewireComponent('forum.pages.category.create', CategoryCreate::class);
        $this->livewireComponent('forum.pages.category.edit', CategoryEdit::class);
        $this->livewireComponent('forum.pages.category.show', CategoryShow::class);
        $this->livewireComponent('forum.pages.category.manage', UpdateCategoryTree::class);
        $this->livewireComponent('forum.pages.thread.create', ThreadCreate::class);
        $this->livewireComponent('forum.pages.thread.reply', ThreadReply::class);
        $this->livewireComponent('forum.pages.thread.show', ThreadShow::class);
        $this->livewireComponent('forum.pages.thread.recent', RecentThreads::class);
        $this->livewireComponent('forum.pages.thread.unread', UnreadThreads::class);
        $this->livewireComponent('forum.pages.thread.pending-approval', ThreadsPendingApproval::class);
        $this->livewireComponent('forum.pages.post.edit', PostEdit::class);
        $this->livewireComponent('forum.pages.post.show', PostShow::class);
        $this->livewireComponent('forum.pages.post.pending-approval', PostsPendingApproval::class);
    }

    public function getRouterConfig(): array
    {
        $config = config('forum.frontend.router');
        $config['middleware'][] = ResolveFrontendParameters::class;

        return $config;
    }

    public function getRoutesPath(): string
    {
        return __DIR__ . '/../../../routes/livewire.php';
    }
}
