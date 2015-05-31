<?php namespace Riari\Forum\Repositories;

use Riari\Forum\Models\Post;

class Posts extends BaseRepository {

    public function __construct(Post $model)
    {
        $this->model = $model;

        $this->itemsPerPage = config('forum.integration.posts_per_thread');
    }

    public function getLastByThread($threadID, $count = 10, array $with = array())
    {
        $model = $this->model->where('parent_thread', '=', $threadID);
        $model = $model->orderBy('created_at', 'DESC')->take($count);
        $model = $model->with($with);

        return $model;
    }

}
