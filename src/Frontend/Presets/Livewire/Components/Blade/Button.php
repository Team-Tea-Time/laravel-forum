<?php

namespace TeamTeaTime\Forum\Frontend\Presets\Livewire\Components\Blade;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public function __construct(
        public string $type = "",
        public string $href = "",
        public string $icon = "",
        public string $label = "",
        public string $colorClasses = "",
    ) {
        $this->colorClasses = match ($this->type) {
            'primary', '', null => 'text-white hover:text-white bg-blue-600 hover:bg-blue-500',
            'secondary' => 'text-zinc-600 hover:text-zinc-600 bg-zinc-300 hover:bg-zinc-200'
        };
    }

    public function render(): View
    {
        return ViewFactory::make('forum::components.button');
    }
}
