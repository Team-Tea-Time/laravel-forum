<?php namespace Riari\Forum\Repositories;

use Riari\Forum\Models\Thread;

class Threads extends BaseRepository
{
    /**
     * Create a new thread repository instance.
     *
     * @param  Post  $model
     */
    public function __construct(Thread $model)
    {
        parent::__construct($model, config('forum.preferences.pagination.threads'));
    }

    /**
     * Get N recently updated threads.
     *
     * @param  array  $where
     * @param  int  $limit
     * @return Collection
     */
    public function getRecent($where = array(), $limit = 100)
    {
        return $this->model->with('category', 'posts')
            ->recent()
            ->where($where)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get new/updated threads for the current user.
     *
     * @param  array  $where
     * @return Collection
     */
    public function getNewForUser($where = array())
    {
        $threads = $this->getRecent($where);

        // If the user is logged in, filter the threads according to read status
        if (auth()->check()) {
            $threads = $threads->filter(function ($thread)
            {
                return $thread->userReadStatus;
            });
        }

        // Filter the threads according to the user's permissions
        $threads = $threads->filter(function ($thread)
        {
            return $thread->category->userCanView;
        });

        return $threads;
    }

    /**
     * Mark new/updated threads for the current user as read.
     *
     * @return void
     */
    public function markNewForUserAsRead()
    {
        if (auth()->check()) {
            $threads = $this->getNewForUser();

            foreach ($threads as $thread) {
                $thread->markAsRead(auth()->user()->id);
            }
        }
    }
}
