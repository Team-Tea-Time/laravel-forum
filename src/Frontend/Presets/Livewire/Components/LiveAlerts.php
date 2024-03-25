<?php

namespace TeamTeaTime\Forum\Frontend\Presets\Livewire\Components;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;

class LiveAlerts extends Component
{
    public function render(): View
    {
        return ViewFactory::make('forum::components.live-alerts');
    }
}
