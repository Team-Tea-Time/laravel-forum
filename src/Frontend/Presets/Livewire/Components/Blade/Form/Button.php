<?php

namespace TeamTeaTime\Forum\Frontend\Presets\Livewire\Components\Blade\Form;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public function __construct(
        public string $icon = "",
        public string $label = "",
        public string $onClick = "",
        public string $wireConfirm = "",
    ) {}

    public function render(): View
    {
        return ViewFactory::make('forum::components.form.button');
    }
}
