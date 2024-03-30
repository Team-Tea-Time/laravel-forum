<?php

namespace TeamTeaTime\Forum\Frontend\Presets\LivewireTailwind\Components;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;

class Pill extends Component
{
    public $bgColor = null;
    public $textColor = null;
    public $padding = null;
    public $margin = null;
    public $icon = null;
    public $text = null;

    public function render(): View
    {
        return ViewFactory::make('forum::components.pill');
    }
}
