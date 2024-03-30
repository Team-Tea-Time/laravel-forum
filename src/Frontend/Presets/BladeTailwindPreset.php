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

    public static function getDescription(): string
    {
        return "Uses Blade with Tailwind CSS for styling.";
    }

    public static function getRequiredStack(): FrontendStack
    {
        return FrontendStack::BLADE;
    }

    public function register(): void
    {
        $this->bladeComponentNamespace("TeamTeaTime\\Forum\\Frontend\\Presets\\BladeTailwind\\Components");
    }
}
