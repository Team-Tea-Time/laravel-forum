<?php

namespace TeamTeaTime\Forum\Frontends;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;
use TeamTeaTime\Forum\ForumServiceProvider;
use TeamTeaTime\Forum\Http\Livewire\Components\Button;
use TeamTeaTime\Forum\Http\Livewire\Components\Category\Card;
use TeamTeaTime\Forum\Http\Livewire\Pages\CategoryIndex;
use TeamTeaTime\Forum\Http\Livewire\Pages\CategoryShow;
use TeamTeaTime\Forum\Http\Livewire\Pages\ThreadCreate;
use TeamTeaTime\Forum\Http\Middleware\ResolveFrontendParameters;

class Livewire implements FrontendInterface
{
    public function register(): void
    {
        // Components
        \Livewire\Livewire::component('components.button', Button::class);
        \Livewire\Livewire::component('components.category.card', Card::class);

        // Pages
        \Livewire\Livewire::component('pages.category.index', CategoryIndex::class);
        \Livewire\Livewire::component('pages.category.show', CategoryShow::class);
        \Livewire\Livewire::component('pages.thread.create', ThreadCreate::class);
    }

    public function configureRouter(Router $router): RouteRegistrar
    {
        $config = config('forum.frontend.router');
        $config['middleware'][] = ResolveFrontendParameters::class;

        return $router
            ->prefix($config['prefix'])
            ->name($config['as'])
            ->middleware($config['middleware']);
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
