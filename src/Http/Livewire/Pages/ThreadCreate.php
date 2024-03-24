<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\Actions\CreateThread as Action;

class ThreadCreate extends Component
{
    // View data
    public $category = null;
    public $breadcrumbs_append = null;

    // Form fields
    public $title = '';
    public $content = '';

    public function mount(Request $request)
    {
        $this->category = $request->route('category');
        $this->breadcrumbs_append = [trans('forum::threads.new_thread')];
    }

    public function save(Request $request)
    {
        $action = new Action($this->category, $request->user(), $this->title, $this->content);
        $thread = $action->execute();

        return $this->redirect('/forum');
    }

    public function render(): View
    {
        return ViewFactory::make('forum::pages.thread.create')
            ->layout('forum::layouts.main');
    }
}
