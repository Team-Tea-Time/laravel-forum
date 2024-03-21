<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use TeamTeaTime\Forum\Events\UserViewingIndex;
use TeamTeaTime\Forum\Support\CategoryPrivacy;

#[Layout('forum::layouts.main')]
class CategoryShow extends Component
{
    public $category = null;

    public function mount(Request $request)
    {
        $this->category = $request->route('category');
    }

    public function render(): View
    {
        return ViewFactory::make('forum::pages.category.show');
    }
}
