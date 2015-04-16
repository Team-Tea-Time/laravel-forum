<?php namespace Riari\Forum\Repositories;

use Config;
use Riari\Forum\Libraries\Utils;
use Riari\Forum\Models\Thread;

class Threads extends BaseRepository {

    public function __construct(Thread $model)
    {
        $this->model = $model;

        $this->itemsPerPage = Config::get('forum::preferences.threads_per_category');
    }

    public function getByID($threadID, $with = array())
    {
        return $this->getFirstBy('id', $threadID, $with);
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
            $threads = $threads->filter(function($thread) use ($userID)
            {
                return $thread->userReadStatus;
            });
        }

        return $threads;
    }

}
