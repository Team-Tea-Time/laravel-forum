<?php

namespace TeamTeaTime\Forum\Frontend;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;
use TeamTeaTime\Forum\ForumServiceProvider;
use TeamTeaTime\Forum\Http\Middleware\ResolveFrontendParameters;

class Blade implements IFrontend
{
    public function register(): void
    {
        // no-op
    }

    public function configureRouter(Router $router): RouteRegistrar
    {
        $config = config('forum.blade.router');
        $config['middleware'][] = ResolveFrontendParameters::class;

        return $router
            ->prefix($config['prefix'])
            ->name($config['as'])
            ->namespace($config['namespace'])
            ->middleware($config['middleware']);
    }

    public function getRoutesPath(): string
    {
        return __DIR__.'/../../routes/blade.php';
    }

    public function getViewsPath(): ?string
    {
        return resource_path('forum/views/blade');
    }
}
