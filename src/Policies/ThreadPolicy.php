<?php

namespace TeamTeaTime\Forum\Policies;

use Illuminate\Support\Facades\Gate;
use TeamTeaTime\Forum\Models\Thread;

class ThreadPolicy
{
    public function deletePosts($user, Thread $thread)
    {
        return true;
    }

    public function rename($user, Thread $thread)
    {
        return $user->getKey() === $thread->author_id;
    }

    public function reply($user, Thread $thread)
    {
        return ! $thread->locked;
    }

    public function delete($user, Thread $thread)
    {
        return Gate::allows('deleteThreads', $thread->category) || $user->getKey() === $thread->author_id;
    }
}
