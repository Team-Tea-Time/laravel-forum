<?php

namespace TeamTeaTime\Forum\Frontend;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;
use TeamTeaTime\Forum\ForumServiceProvider;
use TeamTeaTime\Forum\Http\Livewire\Counter;
use TeamTeaTime\Forum\Http\Middleware\ResolveFrontendParameters;

class Livewire implements IFrontend
{
    public function register(): void
    {
        \Livewire\Livewire::component('counter', Counter::class);
    }

    public function configureRouter(Router $router): RouteRegistrar
    {
        $config = config('forum.livewire.router');
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
        return resource_path('forum/views/livewire');
    }
}
