<?php

namespace TeamTeaTime\Forum\Frontend\Stacks;

use TeamTeaTime\Forum\Http\Middleware\ResolveFrontendParameters;

class Blade implements StackInterface
{
    public function register(): void
    {
        // no-op
    }

    public function getRouterConfig(): array
    {
        $config = config('forum.frontend.router');
        $config['middleware'][] = ResolveFrontendParameters::class;

        return $config;
    }

    public function getRoutesPath(): string
    {
        return __DIR__ . '/../../../routes/blade.php';
    }
}
