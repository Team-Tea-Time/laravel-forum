<?php

namespace TeamTeaTime\Forum\Frontend\Presets;

use TeamTeaTime\Forum\Config\FrontendStack;

class BootstrapPreset extends AbstractPreset
{
    public static function getName(): string
    {
        return 'bootstrap';
    }

    public static function getDescription(): string
    {
        return "Uses Blade with Bootstrap for styling.";
    }

    public static function getRequiredStack(): FrontendStack
    {
        return FrontendStack::BLADE;
    }
}
