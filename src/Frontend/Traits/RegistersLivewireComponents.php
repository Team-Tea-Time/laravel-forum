<?php

namespace TeamTeaTime\Forum\Frontend\Traits;

trait RegistersLivewireComponents
{
    private function livewireComponent(string $name, string $class): void
    {
        \Livewire\Livewire::addComponent(
            name: $name,
            class: $class
        );
    }
}
