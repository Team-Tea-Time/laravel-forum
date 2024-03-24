<?php

namespace TeamTeaTime\Forum\Presets;

use TeamTeaTime\Forum\Config\FrontendStack;

class TailwindPreset extends AbstractPreset
{
    public function name(): string
    {
        return 'tailwind';
    }

    public function description(): string
    {
        return "Similar to the Blade preset, but uses Tailwind CSS for the styling.";
    }

    public function requiredStack(): FrontendStack
    {
        return FrontendStack::BLADE;
    }
}
