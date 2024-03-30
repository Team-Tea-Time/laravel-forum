<?php

namespace TeamTeaTime\Forum\Frontend\Presets\BladeTailwind\Components;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public function __construct(
    )
    {
    }

    public function render(): View
    {
        return ViewFactory::make('forum::components.button');
    }
}
