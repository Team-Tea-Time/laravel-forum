<?php

namespace TeamTeaTime\Forum\Frontend\Presets;

use TeamTeaTime\Forum\{
    Config\FrontendStack,
    Frontend\Presets\Livewire\Components\Category\Card as CategoryCard,
    Frontend\Presets\Livewire\Components\Thread\Card as ThreadCard,
    Frontend\Presets\Livewire\Components\Button,
    Frontend\Presets\Livewire\Components\Pill,
    Frontend\Traits\LivewireTrait,
};

class LivewirePreset extends AbstractPreset
{
    use LivewireTrait;

    public static function getName(): string
    {
        return 'livewire';
    }

    public static function getDescription(): string
    {
        return "Uses Blade with Livewire and Tailwind CSS for styling.";
    }

    public static function getRequiredStack(): FrontendStack
    {
        return FrontendStack::LIVEWIRE;
    }

    public function register(): void
    {
        $this->registerComponent('components.category.card', CategoryCard::class);
        $this->registerComponent('components.thread.card', ThreadCard::class);
        $this->registerComponent('components.button', Button::class);
        $this->registerComponent('components.pill', Pill::class);
    }
}
