<?php

namespace TeamTeaTime\Forum\Frontend\Presets;

use TeamTeaTime\Forum\{
    Config\FrontendStack,
    Frontend\Traits\RegistersBladeComponents,
};

class BladeTailwindPreset extends AbstractPreset
{
    use RegistersBladeComponents;

    public static function getName(): string
    {
        return 'blade-tailwind';
    }

    public static function getSummary(): string
    {
        return "Blade with Vue and Tailwind CSS.";
    }

    public static function getRequiredStack(): FrontendStack
    {
        return FrontendStack::BLADE;
    }

    public static function getRequiredPackages(): array
    {
        return [
            '@simonwep/pickr',
            'axios',
            'feather-icons',
            'tailwindcss',
            'vue',
            'vuedraggable@next',
        ];
    }

    public function register(): void
    {
        $this->bladeComponentNamespace("TeamTeaTime\\Forum\\Frontend\\Presets\\BladeTailwind\\Components");
    }
}
