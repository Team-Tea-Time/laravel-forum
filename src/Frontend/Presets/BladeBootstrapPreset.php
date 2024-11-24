<?php

namespace TeamTeaTime\Forum\Frontend\Presets;

use TeamTeaTime\Forum\Config\FrontendStack;

class BladeBootstrapPreset extends AbstractPreset
{
    public static function getName(): string
    {
        return 'blade-bootstrap';
    }

    public static function getSummary(): string
    {
        return "Blade with Vue and Bootstrap.";
    }

    public static function getRequiredStack(): FrontendStack
    {
        return FrontendStack::BLADE;
    }

    public static function getViteInput(): array
    {
        return [
            'resources/forum/blade-bootstrap/css/forum.css',
            'resources/forum/blade-bootstrap/js/forum.js',
        ];
    }

    public static function getRequiredPackages(): array
    {
        return [
            '@simonwep/pickr',
            'axios',
            'bootstrap',
            'feather-icons',
            'vue',
            'vuedraggable@next',
        ];
    }
}
