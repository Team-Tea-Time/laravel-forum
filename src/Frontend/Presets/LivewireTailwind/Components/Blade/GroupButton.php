<?php

namespace TeamTeaTime\Forum\Frontend\Presets\LivewireTailwind\Components\Blade;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Illuminate\View\Component;

class GroupButton extends Component
{
    public function __construct(
        public string $intent = "",
        public string $href = "#",
        public string $icon = "",
        public string $label = "",
    )
    {
    }

    public function render(): View
    {
        return ViewFactory::make('forum::components.group-button');
    }
}
