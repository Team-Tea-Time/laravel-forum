<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Actions\EditPost as Action,
    Events\UserEditingPost,
    Events\UserEditedPost,
    Models\Post,
    Support\Authorization\PostAuthorization,
    Support\Validation\CategoryRules,
    Support\Frontend\Forum,
};

class PostEdit extends Component
{
    #[Locked]
    public Post $post;

    // Form fields
    public string $content;

    public function mount(Request $request)
    {
        $this->post = $request->route('post');

        if (!PostAuthorization::edit($request->user(), $this->post)) {
            abort(404);
        }

        if ($request->user() !== null) {
            UserEditingPost::dispatch($request->user(), $request->route('post'));
        }
    }

    public function save(Request $request)
    {
        if (!PostAuthorization::edit($request->user(), $this->post)) {
            abort(403);
        }

        $validated = $this->validate(CategoryRules::create());

        $action = new Action($this->post, $validated['content']);
        $action->execute();

        UserEditedPost::dispatch($request->user(), $this->post);

        return $this->redirect($this->post->route);
    }

    public function delete(Request $request)
    {
        if (!PostAuthorization::delete($request->user(), $this->category)) {
            abort(403);
        }

        $thread = $this->post->thread;

        $this->post->delete();

        return $this->redirect($thread->route);
    }

    public function render(): View
    {
        return ViewFactory::make('forum::pages.post.edit')
            ->layout('forum::layouts.main');
    }
}
