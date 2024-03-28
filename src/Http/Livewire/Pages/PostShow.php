<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Events\UserViewingPost,
};

class PostShow extends Component
{
    public function render(Request $request): View
    {
        $post = $request->route('post');

        if (!$post->thread->category->isAccessibleTo($request->user())) {
            abort(404);
        }

        if ($request->user() !== null) {
            UserViewingPost::dispatch($request->user(), $post);
        }

        return ViewFactory::make('forum::pages.post.show', [
            'post' => $post,
        ])->layout('forum::layouts.main', [
            'category' => $post->thread->category,
            'thread' => $post->thread,
            'post' => $post,
        ]);
    }
}
