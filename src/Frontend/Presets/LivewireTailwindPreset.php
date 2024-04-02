<?php

namespace TeamTeaTime\Forum\Frontend\Presets;

use TeamTeaTime\Forum\{
    Config\FrontendStack,
    Frontend\Presets\LivewireTailwind\Components\Category\Card as CategoryCard,
    Frontend\Presets\LivewireTailwind\Components\Post\Card as PostCard,
    Frontend\Presets\LivewireTailwind\Components\Post\Quote as PostQuote,
    Frontend\Presets\LivewireTailwind\Components\Thread\Card as ThreadCard,
    Frontend\Presets\LivewireTailwind\Components\Alerts,
    Frontend\Presets\LivewireTailwind\Components\Pill,
    Frontend\Presets\LivewireTailwind\Components\Timestamp,
    Frontend\Traits\RegistersBladeComponents,
    Frontend\Traits\RegistersLivewireComponents,
};

class LivewireTailwindPreset extends AbstractPreset
{
    use RegistersBladeComponents, RegistersLivewireComponents;

    public static function getName(): string
    {
        return 'livewire-tailwind';
    }

    public static function getSummary(): string
    {
        return "Blade with Livewire, AlpineJS, and Tailwind CSS.";
    }

    public static function getRequiredStack(): FrontendStack
    {
        return FrontendStack::LIVEWIRE;
    }

    public static function getRequiredPackages(): array
    {
        return [
            '@melloware/coloris',
            'alpinejs',
            'date-fns',
            'laravel-echo',
            'nested-sort',
            'tailwindcss',
        ];
    }

    public function register(): void
    {
        $this->bladeComponentNamespace("TeamTeaTime\\Forum\\Frontend\\Presets\\LivewireTailwind\\Components\\Blade");

        $this->livewireComponent('components.category.card', CategoryCard::class);
        $this->livewireComponent('components.post.card', PostCard::class);
        $this->livewireComponent('components.post.quote', PostQuote::class);
        $this->livewireComponent('components.thread.card', ThreadCard::class);
        $this->livewireComponent('components.alerts', Alerts::class);
        $this->livewireComponent('components.pill', Pill::class);
        $this->livewireComponent('components.timestamp', Timestamp::class);
    }
}
