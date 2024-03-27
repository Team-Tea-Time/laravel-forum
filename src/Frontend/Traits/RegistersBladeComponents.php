<?php

namespace TeamTeaTime\Forum\Frontend\Traits;

use Illuminate\Support\Facades\Blade;

trait RegistersBladeComponents
{
    private function bladeComponent(string $name, string $component): void
    {
        Blade::component("forum::{$name}", $component);
    }

    private function bladeComponentNamespace(string $namespace): void
    {
        Blade::componentNamespace($namespace, 'forum');
    }
}
