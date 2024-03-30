<?php

namespace TeamTeaTime\Forum\Frontend\Presets\LivewireTailwind\Components\Post;

use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\Models\Post;

class Card extends Component
{
    public Post $post;
    public bool $showAuthorPane = true;
    public bool $selectable;
    public bool $single;

    public function render(): View
    {
        return ViewFactory::make('forum::components.post.card');
    }
}
