<?php

namespace TeamTeaTime\Forum\Frontend\Traits;

use Illuminate\Support\Facades\Blade;

trait BladeTrait
{
    private function bladeComponent(string $name, string $component): void
    {
        Blade::component("forum::{$name}", $component);
    }
}
