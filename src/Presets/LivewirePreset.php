<?php

namespace TeamTeaTime\Forum\Presets;

use TeamTeaTime\Forum\Config\FrontendStack;

class LivewirePreset extends AbstractPreset
{
    public function name(): string
    {
        return 'livewire';
    }

    public function description(): string
    {
        return "A preset that uses the Livewire stack with real-time updates via broadcasted events.";
    }

    public function requiredStack(): FrontendStack
    {
        return FrontendStack::LIVEWIRE;
    }
}
