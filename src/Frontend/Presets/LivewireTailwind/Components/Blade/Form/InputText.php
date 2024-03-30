<?php

namespace TeamTeaTime\Forum\Frontend\Presets\LivewireTailwind\Components\Blade\Form;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;

class InputText extends InputComponent
{
    public function render(): View
    {
        return ViewFactory::make('forum::components.form.input-text');
    }
}
