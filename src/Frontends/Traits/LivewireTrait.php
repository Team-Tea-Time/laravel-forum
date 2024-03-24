<?php

namespace TeamTeaTime\Forum\Frontends\Traits;

trait LivewireTrait
{
    private function registerComponent(string $name, string $component): void
    {
        \Livewire\Livewire::component("forum::{$name}", $component);
    }
}
