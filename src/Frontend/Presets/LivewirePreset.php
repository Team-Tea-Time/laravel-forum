<?php

namespace TeamTeaTime\Forum\Frontend\Presets;

use TeamTeaTime\Forum\Config\FrontendStack;

class LivewirePreset extends AbstractPreset
{
    public static function getName(): string
    {
        return 'livewire';
    }

    public static function getDescription(): string
    {
        return "A preset that uses the Livewire stack with real-time updates via broadcasted events.";
    }

    public static function getRequiredStack(): FrontendStack
    {
        return FrontendStack::LIVEWIRE;
    }
}
