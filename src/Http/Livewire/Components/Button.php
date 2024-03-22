<?php

namespace TeamTeaTime\Forum\Http\Livewire\Components;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;

class Button extends Component
{
    public $href = null;
    public $label = null;

    public function render(): View
    {
        return ViewFactory::make('forum::components.button');
    }
}
