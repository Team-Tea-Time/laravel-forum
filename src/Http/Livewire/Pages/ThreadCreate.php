<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use TeamTeaTime\Forum\Actions\CreateThread as Action;
use TeamTeaTime\Forum\Support\Validation\ThreadRules;

class ThreadCreate extends Component
{
    // View data
    #[Locked]
    public $category = null;
    #[Locked]
    public $breadcrumbs_append = null;

    // Form fields
    public string $title = '';
    public string $content = '';

    public function mount(Request $request)
    {
        $this->category = $request->route('category');
        $this->breadcrumbs_append = [trans('forum::threads.new_thread')];
    }

    public function save(Request $request)
    {
        if (!$this->category->accepts_threads || !$request->user()->can('createThreads', $this->category)) {
            abort(403);
        }

        $validated = $this->validate(ThreadRules::create());

        $action = new Action($this->category, $request->user(), $validated['title'], $validated['content']);
        $thread = $action->execute();

        return $this->redirect($thread->route);
    }

    public function render(): View
    {
        return ViewFactory::make('forum::pages.thread.create')
            ->layout('forum::layouts.main');
    }
}
