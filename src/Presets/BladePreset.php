<?php

namespace TeamTeaTime\Forum\Presets;

use TeamTeaTime\Forum\Config\FrontendStack;

class BladePreset extends AbstractPreset
{
    public function name(): string
    {
        return 'blade';
    }

    public function description(): string
    {
        return "A simple preset that uses Laravel's built-in Blade views. Uses Bootstrap for styling.";
    }

    public function requiredStack(): FrontendStack
    {
        return FrontendStack::BLADE;
    }
}
