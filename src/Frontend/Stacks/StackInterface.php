<?php

namespace TeamTeaTime\Forum\Frontend\Stacks;

interface StackInterface
{
    /**
     * Called by the service provider. Implement this to register any components required by the preset.
     */
    public function register(): void;

    /**
     * Returns a config array for the router group that wraps the routes for this stack.
     */
    public function getRouterConfig(): array;

    /**
     * Returns the path to the routes that should be registered for this stack.
     */
    public function getRoutesPath(): string;
}
