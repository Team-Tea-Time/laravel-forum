<?php

namespace TeamTeaTime\Forum\Frontends;

interface FrontendInterface
{
    public function register(): void;
    public function getRouterConfig(): array;
    public function getRoutesPath(): string;
    public function getViewsPath(): ?string;
}
