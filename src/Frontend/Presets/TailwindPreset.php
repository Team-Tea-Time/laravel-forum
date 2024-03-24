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
        return "Uses Blade with Tailwind CSS for styling.";
    }

    public static function getRequiredStack(): FrontendStack
    {
        return FrontendStack::BLADE;
    }
}
