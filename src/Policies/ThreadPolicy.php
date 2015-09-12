<?php

namespace Riari\Forum\Policies;

use Riari\Forum\Models\Thread;

class ThreadPolicy
{
    /**
     * Permission: Edit thread (title).
     *
     * @param  object  $user
     * @param  Thread  $thread
     * @return bool
     */
    public function edit($user, Thread $thread)
    {
        return $user->id === $thread->user_id;
    }

    /**
     * Permission: Reply to thread.
     *
     * @param  object  $user
     * @param  Thread  $thread
     * @return bool
     */
    public function reply($user, Thread $thread)
    {
        return !$thread->locked;
    }

    /**
     * Permission: Delete thread.
     *
     * @param  object  $user
     * @param  Thread  $thread
     * @return bool
     */
    public function delete($user, Thread $thread)
    {
        return $user->id === $thread->user_id;
    }
}
