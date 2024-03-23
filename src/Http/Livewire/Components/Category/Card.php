<?php

namespace TeamTeaTime\Forum\Http\Livewire\Components\Category;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\Models\Category;

class Card extends Component
{
    public Category $category;

    public function render(): View
    {
        return ViewFactory::make('forum::components.category.card');
    }
}
