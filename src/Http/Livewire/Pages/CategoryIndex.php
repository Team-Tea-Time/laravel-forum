<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\Events\UserViewingIndex;
use TeamTeaTime\Forum\Support\Access\CategoryAccess;

class CategoryIndex extends Component
{
    public $categories = [];

    public function mount(Request $request)
    {
        $categories = CategoryAccess::getFilteredTreeFor($request->user())->toTree();

        // TODO: This is a workaround for a serialisation issue. See: https://github.com/lazychaser/laravel-nestedset/issues/487
        //       Once the issue is fixed, this can be removed.
        $this->categories = CategoryAccess::removeParentRelationships($categories);

        if ($request->user() !== null) {
            UserViewingIndex::dispatch($request->user());
        }
    }

    public function render(): View
    {
        return ViewFactory::make('forum::pages.category.index')
            ->layout('forum::layouts.main');
    }
}
