<?php

namespace TeamTeaTime\Forum\Frontend\Stacks;

use TeamTeaTime\Forum\{
    Http\Livewire\Pages\CategoryIndex,
    Http\Livewire\Pages\CategoryShow,
    Http\Livewire\Pages\ThreadCreate,
    Http\Livewire\Pages\ThreadShow,
    Http\Middleware\ResolveFrontendParameters,
    Frontend\Traits\LivewireTrait,
};

class Livewire implements StackInterface
{
    use LivewireTrait;

    public function register(): void
    {
        // Register full-page components required by the Livewire routes
        $this->registerComponent('pages.category.index', CategoryIndex::class);
        $this->registerComponent('pages.category.show', CategoryShow::class);
        $this->registerComponent('pages.thread.create', ThreadCreate::class);
        $this->registerComponent('pages.thread.show', ThreadShow::class);
    }

    public function getRouterConfig(): array
    {
        $config = config('forum.frontend.router');
        $config['middleware'][] = ResolveFrontendParameters::class;

        return $config;
    }

    public function getRoutesPath(): string
    {
        return __DIR__.'/../../../routes/livewire.php';
    }
}
