<?php

namespace TeamTeaTime\Forum\Http\Requests;

use TeamTeaTime\Forum\{
    Actions\EditPost as Action,
    Events\UserEditedPost,
};

class EditPost extends CreatePost
{
    public function authorize(): bool
    {
        return $this->user()->can('edit', $this->route('post'));
    }

    public function fulfill()
    {
        $input = $this->validated();
        $action = new Action($this->route('post'), $input['content']);
        $post = $action->execute();

        UserEditedPost::dispatch($this->user(), $post);

        return $post;
    }
}
