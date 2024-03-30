<?php

namespace TeamTeaTime\Forum\Frontend\Presets\LivewireTailwind\Components\Blade\Form;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;

class InputSelect extends InputComponent
{
    public function __construct(
        public string $id = "",
        public string $label = "",
        public string $value = "",
        public string $xShow = "",
        public array $options = [],
    )
    {
    }

    public function render(): View
    {
        return ViewFactory::make('forum::components.form.input-select');
    }
}
