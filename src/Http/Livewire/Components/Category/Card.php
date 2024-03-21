<?php

namespace TeamTeaTime\Forum\Http\Livewire\Components\Category;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;

class Card extends Component
{
    public $category = null;

    public function render(): View
    {
        return ViewFactory::make('forum::components.category.card');
    }
}
