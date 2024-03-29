<?php

namespace TeamTeaTime\Forum\Http\Livewire\Forms;

use Illuminate\Http\Request;
use Livewire\Form;
use TeamTeaTime\Forum\{
    Actions\CreatePost,
    Events\UserCreatedPost,
    Models\Post,
    Models\Thread,
    Support\Authorization\ThreadAuthorization,
    Support\Validation\PostRules,
};

class ThreadReplyForm extends Form
{
    public string $content = '';

    public function reply(Request $request, Thread $thread): Post
    {
        if (!ThreadAuthorization::reply($request->user(), $thread)) {
            abort(403);
        }

        $validated = $this->validate(PostRules::create());
        $parent = $request->has('post')
            ? $thread->posts->find($request->input('post'))
            : null;

        $action = new CreatePost($thread, $parent, $request->user(), $validated['content']);
        $post = $action->execute();

        $post->thread->markAsRead($request->user());

        UserCreatedPost::dispatch($request->user(), $post);

        $this->content = '';

        return $post;
    }
}
