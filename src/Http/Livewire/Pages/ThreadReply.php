<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Actions\CreatePost as Action,
    Events\UserCreatedPost,
    Events\UserCreatingPost,
    Http\Livewire\Traits\CreatesAlerts,
    Http\Livewire\Traits\UpdatesContent,
    Models\Post,
    Models\Thread,
    Support\Validation\PostRules,
};

class ThreadReply extends Component
{
    use CreatesAlerts, UpdatesContent;

    #[Locked]
    public Thread $thread;

    #[Locked]
    public ?Post $parent = null;

    // Form fields
    public string $content = '';

    public function mount(Request $request)
    {
        $this->thread = $request->route('thread');

        if (!$this->thread->category->isAccessibleTo($request->user())) {
            abort(404);
        }

        if ($request->input('parent_id')) {
            $this->parent = $this->thread->posts->find($request->input('parent_id'));
        }

        if ($request->user() !== null) {
            UserCreatingPost::dispatch($request->user(), $this->thread);
        }
    }

    public function reply(Request $request)
    {
        if (!$request->user()->can('reply', $this->thread)) {
            abort(403);
        }

        $validated = $this->validate(PostRules::create());
        $action = new Action($this->thread, $this->parent, $request->user(), $validated['content']);
        $post = $action->execute();

        $post->thread->markAsRead($request->user());

        UserCreatedPost::dispatch($request->user(), $post);

        $this->content = "";

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
