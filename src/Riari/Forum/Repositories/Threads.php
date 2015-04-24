<?php namespace Riari\Forum\Repositories;

use Riari\Forum\Models\Thread;

class Threads extends BaseRepository {

    public function __construct(Thread $model)
    {
        $this->model = $model;

        $this->itemsPerPage = config('forum.integration.threads_per_category');
    }

    public function getRecent($where = array())
    {
        return $this->model->with('category', 'posts')->recent()->where($where)->orderBy('updated_at', 'desc')->get();
    }

    public function getNewForUser($userID = 0, $where = array())
    {
        $threads = $this->getRecent($where);

        // If we have a user ID, filter the threads appropriately
        if ($userID)
        {
            $threads = $threads->filter(function($thread)
            {
                return $thread->userReadStatus;
            });
        }

        // Filter the threads according to the user's permissions
        $threads = $threads->filter(function($thread)
        {
            return $thread->category->userCanView;
        });

        return $threads;
    }

}
