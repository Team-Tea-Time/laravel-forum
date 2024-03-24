<?php

namespace TeamTeaTime\Forum\Frontend\Presets;

use TeamTeaTime\Forum\Config\FrontendStack;

class TailwindPreset extends AbstractPreset
{
    public static function getName(): string
    {
        return 'tailwind';
    }

    public static function getDescription(): string
    {
        return "Similar to the Blade preset, but uses Tailwind CSS for the styling.";
    }

    public static function getRequiredStack(): FrontendStack
    {
        return FrontendStack::BLADE;
    }
}
