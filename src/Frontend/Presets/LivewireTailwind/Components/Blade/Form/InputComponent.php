<?php

namespace TeamTeaTime\Forum\Frontend\Presets\LivewireTailwind\Components\Blade\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputComponent extends Component
{
    public function __construct(
        public string $id = "",
        public string $label = "",
        public string $value = "",
        public string $xShow = "",
    )
    {
    }

    public function render(): View|Closure|string
    {
        return '';
    }
}
