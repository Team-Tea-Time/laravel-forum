<?php

namespace TeamTeaTime\Forum\Frontend\Presets\Livewire\Components\Thread;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\Models\Thread;

class Card extends Component
{
    public Thread $thread;

    public function render(): View
    {
        return ViewFactory::make('forum::components.thread.card');
    }
}
