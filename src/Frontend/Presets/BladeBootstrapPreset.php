<?php

namespace TeamTeaTime\Forum\Frontend\Presets;

use TeamTeaTime\Forum\Config\FrontendStack;

class BladeBootstrapPreset extends AbstractPreset
{
    public static function getName(): string
    {
        return 'blade-bootstrap';
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
