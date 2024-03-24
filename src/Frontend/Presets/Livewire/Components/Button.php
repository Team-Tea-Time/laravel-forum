<?php

namespace TeamTeaTime\Forum\Frontend\Presets\Livewire\Components;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;

class Button extends Component
{
    public $href = null;
    public $icon = null;
    public $label = null;

    public function render(): View
    {
        return ViewFactory::make('forum::components.button');
    }
}
