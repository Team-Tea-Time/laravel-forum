<?php

namespace TeamTeaTime\Forum\Frontend\Presets\Livewire\Components\Post;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\Models\Post;

class Card extends Component
{
    public Post $post;

    public function render(): View
    {
        return ViewFactory::make('forum::components.post.card');
    }
}
