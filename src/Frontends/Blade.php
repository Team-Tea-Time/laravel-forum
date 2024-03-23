<?php

namespace TeamTeaTime\Forum\Frontends;

use TeamTeaTime\Forum\Http\Middleware\ResolveFrontendParameters;

class Blade implements FrontendInterface
{
    public function register(): void
    {
        // no-op
    }

    public function getRouterConfig(): array
    {
        $config = config('forum.frontend.router');
        $config['middleware'][] = ResolveFrontendParameters::class;
        $config['namespace'] = 'TeamTeaTime\Forum\Http\Controllers\Blade';

        return $config;
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
