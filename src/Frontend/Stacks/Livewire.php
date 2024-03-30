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
    Http\Livewire\Pages\PostEdit,
    Http\Livewire\Pages\PostShow,
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
        // Register full-page components required by the Livewire routes
        $this->livewireComponent('pages.category.create', CategoryCreate::class);
        $this->livewireComponent('pages.category.edit', CategoryEdit::class);
        $this->livewireComponent('pages.category.index', CategoryIndex::class);
        $this->livewireComponent('pages.category.show', CategoryShow::class);
        $this->livewireComponent('pages.category.manage', UpdateCategoryTree::class);
        $this->livewireComponent('pages.thread.create', ThreadCreate::class);
        $this->livewireComponent('pages.thread.reply', ThreadReply::class);
        $this->livewireComponent('pages.thread.show', ThreadShow::class);
        $this->livewireComponent('pages.thread.recent', RecentThreads::class);
        $this->livewireComponent('pages.thread.unread', UnreadThreads::class);
        $this->livewireComponent('pages.post.edit', PostEdit::class);
        $this->livewireComponent('pages.post.show', PostShow::class);
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
