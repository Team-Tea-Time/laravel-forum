<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Events\UserCreatingPost,
    Http\Livewire\Forms\ThreadReplyForm,
    Http\Livewire\Traits\CreatesAlerts,
    Http\Livewire\Traits\UpdatesContent,
    Models\Post,
    Models\Thread,
};

class ThreadReply extends Component
{
    use CreatesAlerts, UpdatesContent;

    #[Locked]
    public Thread $thread;

    #[Locked]
    public ?Post $parent = null;

    public ThreadReplyForm $form;

    public function mount(Request $request)
    {
        $this->thread = $request->route('thread')->load('category');

        if (!$this->thread->category->isAccessibleTo($request->user())) {
            abort(404);
        }

        if ($request->input('parent_id')) {
            $this->parent = $this->thread->posts()->find($request->input('parent_id'));
        }

        UserCreatingPost::dispatch($request->user(), $this->thread);
    }

    public function reply(Request $request)
    {
        $post = $this->form->reply($request, $this->thread, $this->parent);

        return redirect($post->route);
    }

    public function render(Request $request): View
    {
        return ViewFactory::make('forum::pages.thread.reply', [
            'thread' => $this->thread,
            'breadcrumbs_append' => [trans('forum::general.reply')],
        ])->layout('forum::layouts.main', [
            'category' => $this->thread->category,
            'thread' => $this->thread
        ]);
    }
}
