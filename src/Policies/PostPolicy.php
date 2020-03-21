<?php namespace TeamTeaTime\Forum\Policies;

use Illuminate\Support\Facades\Gate;
use TeamTeaTime\Forum\Models\Post;

class PostPolicy
{
    public function edit($user, Post $post): bool
    {
        return $user->getKey() === $post->author_id;
    }

    public function delete($user, Post $post): bool
    {
        return Gate::forUser($user)->allows('deletePosts', $post->thread) || $user->getKey() === $post->author_id;
    }

    public function restore($user, Post $post): bool
    {
        return Gate::forUser($user)->allows('restorePosts', $post->thread) || $user->getKey() === $post->author_id;
    }
}
