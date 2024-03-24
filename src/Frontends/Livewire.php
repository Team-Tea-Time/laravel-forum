<?php

namespace TeamTeaTime\Forum\Frontends;

use TeamTeaTime\Forum\Http\Livewire\Components\Button;
use TeamTeaTime\Forum\Http\Livewire\Components\Category\Card as CategoryCard;
use TeamTeaTime\Forum\Http\Livewire\Components\Thread\Card as ThreadCard;
use TeamTeaTime\Forum\Http\Livewire\Pages\CategoryIndex;
use TeamTeaTime\Forum\Http\Livewire\Pages\CategoryShow;
use TeamTeaTime\Forum\Http\Livewire\Pages\ThreadCreate;
use TeamTeaTime\Forum\Http\Middleware\ResolveFrontendParameters;
use TeamTeaTime\Forum\Frontends\Traits\LivewireTrait;

class Livewire implements FrontendInterface
{
    use LivewireTrait;

    public function register(): void
    {
        // Components
        $this->registerComponent('components.button', Button::class);
        $this->registerComponent('components.category.card', CategoryCard::class);
        $this->registerComponent('components.thread.card', ThreadCard::class);

        // Pages
        $this->registerComponent('pages.category.index', CategoryIndex::class);
        $this->registerComponent('pages.category.show', CategoryShow::class);
        $this->registerComponent('pages.thread.create', ThreadCreate::class);
    }

    public function getRouterConfig(): array
    {
        $config = config('forum.frontend.router');
        $config['middleware'][] = ResolveFrontendParameters::class;

        return $config;
    }

    public function getRoutesPath(): string
    {
        return __DIR__.'/../../routes/livewire.php';
    }

    public function getViewsPath(): ?string
    {
        return resource_path('forum/livewire/views');
    }
}
