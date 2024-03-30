<?php

namespace TeamTeaTime\Forum\Frontend\Presets\LivewireTailwind\Components\Blade;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public function __construct(
        public string $heading = "",
        public string $onClose = "",
    )
    {
    }

    public function render(): View
    {
        return ViewFactory::make('forum::components.modal');
    }
}
