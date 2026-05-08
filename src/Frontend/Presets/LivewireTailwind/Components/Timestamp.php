<?php

namespace TeamTeaTime\Forum\Frontend\Presets\LivewireTailwind\Components;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;

class Timestamp extends Component
{
    public CarbonInterface $carbon;

    public function render(): View
    {
        return ViewFactory::make('forum::components.timestamp');
    }
}
