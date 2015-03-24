<?php namespace Riari\Forum\Repositories;

use Riari\Forum\Models\Thread;

use Config;

class Threads extends BaseRepository {

    public function __construct(Thread $model)
    {
        $this->model = $model;

        $this->itemsPerPage = config('forum.integration.threads_per_category');
    }

    public function getByID($threadID, $with = array())
    {
        return $this->getFirstBy('id', $threadID, $with);
    }

}
