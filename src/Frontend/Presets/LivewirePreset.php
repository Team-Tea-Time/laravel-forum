<?php

namespace TeamTeaTime\Forum\Frontend\Presets;

use TeamTeaTime\Forum\{
    Config\FrontendStack,
    Frontend\Presets\Livewire\Components\Category\Card as CategoryCard,
    Frontend\Presets\Livewire\Components\Post\Card as PostCard,
    Frontend\Presets\Livewire\Components\Thread\Card as ThreadCard,
    Frontend\Presets\Livewire\Components\Alerts,
    Frontend\Presets\Livewire\Components\Pill,
    Frontend\Presets\Livewire\Components\Timestamp,
    Frontend\Traits\RegistersBladeComponents,
    Frontend\Traits\RegistersLivewireComponents,
};

class LivewirePreset extends AbstractPreset
{
    use RegistersBladeComponents, RegistersLivewireComponents;

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
        $this->bladeComponentNamespace("TeamTeaTime\\Forum\\Frontend\\Presets\\Livewire\\Components\\Blade");

        $this->livewireComponent('components.category.card', CategoryCard::class);
        $this->livewireComponent('components.post.card', PostCard::class);
        $this->livewireComponent('components.thread.card', ThreadCard::class);
        $this->livewireComponent('components.alerts', Alerts::class);
        $this->livewireComponent('components.pill', Pill::class);
        $this->livewireComponent('components.timestamp', Timestamp::class);
    }
}
