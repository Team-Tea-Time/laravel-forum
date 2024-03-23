<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use TeamTeaTime\Forum\Events\UserViewingIndex;
use TeamTeaTime\Forum\Support\CategoryAccess;

#[Layout('forum::layouts.main')]
class CategoryIndex extends Component
{
    public $categories = [];

    public function mount(Request $request)
    {
        $this->categories = CategoryAccess::getFilteredTreeFor($request->user());

        if ($request->user() !== null) {
            UserViewingIndex::dispatch($request->user());
        }
    }

    public function render(): View
    {
        return ViewFactory::make('forum::pages.category.index');
    }
}
