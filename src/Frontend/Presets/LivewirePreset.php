<?php

namespace TeamTeaTime\Forum\Frontend\Presets;

use TeamTeaTime\Forum\{
    Config\FrontendStack,
    Frontend\Presets\Livewire\Components\Category\Card as CategoryCard,
    Frontend\Presets\Livewire\Components\Post\Card as PostCard,
    Frontend\Presets\Livewire\Components\Thread\Card as ThreadCard,
    Frontend\Presets\Livewire\Components\Button,
    Frontend\Presets\Livewire\Components\LiveAlerts,
    Frontend\Presets\Livewire\Components\Pill,
    Frontend\Traits\RegistersLivewireComponents,
};

class LivewirePreset extends AbstractPreset
{
    use RegistersLivewireComponents;

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
        $this->livewireComponent('components.category.card', CategoryCard::class);
        $this->livewireComponent('components.post.card', PostCard::class);
        $this->livewireComponent('components.thread.card', ThreadCard::class);
        $this->livewireComponent('components.button', Button::class);
        $this->livewireComponent('components.live-alerts', LiveAlerts::class);
        $this->livewireComponent('components.pill', Pill::class);
    }
}
