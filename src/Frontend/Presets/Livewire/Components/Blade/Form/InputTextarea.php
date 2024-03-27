<?php

namespace TeamTeaTime\Forum\Frontend\Presets\Livewire\Components\Blade\Form;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;

class InputTextarea extends InputComponent
{
    public function render(): View
    {
        return ViewFactory::make('forum::components.form.input-textarea');
    }
}
