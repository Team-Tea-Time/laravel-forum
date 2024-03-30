<?php

namespace TeamTeaTime\Forum\Frontend\Presets\LivewireTailwind\Components\Blade\Form;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;

class InputCheckbox extends InputComponent
{
    public function render(): View
    {
        return ViewFactory::make('forum::components.form.input-checkbox');
    }
}
