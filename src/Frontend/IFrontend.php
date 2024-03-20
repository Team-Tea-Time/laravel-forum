<?php

namespace TeamTeaTime\Forum\Frontend;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;
use TeamTeaTime\Forum\ForumServiceProvider;

interface IFrontend
{
    public function register(): void;
    public function configureRouter(Router $router): RouteRegistrar;
    public function getRoutesPath(): string;
    public function getViewsPath(): ?string;
}
