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
class CategoryIndex extends Component
{
    public $categories = [];

    public function mount(Request $request)
    {
        $this->categories = CategoryPrivacy::getFilteredTreeFor($request->user())->toArray();

        if ($request->user() !== null) {
            UserViewingIndex::dispatch($request->user());
        }
    }

    public function render(): View
    {
        return ViewFactory::make('forum::pages.category.index');
    }
}
