<?php

namespace TeamTeaTime\Forum\Frontend\Presets;

use TeamTeaTime\Forum\Config\FrontendStack;

class BladePreset extends AbstractPreset
{
    public static function getName(): string
    {
        return 'blade';
    }

    public static function getDescription(): string
    {
        return "A simple preset that uses Laravel's built-in Blade views. Uses Bootstrap for styling.";
    }

    public static function getRequiredStack(): FrontendStack
    {
        return FrontendStack::BLADE;
    }
}
