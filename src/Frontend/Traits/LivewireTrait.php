<?php

namespace TeamTeaTime\Forum\Frontend\Traits;

trait LivewireTrait
{
    private function livewireComponent(string $name, string $component): void
    {
        \Livewire\Livewire::component("forum::{$name}", $component);
    }
}
