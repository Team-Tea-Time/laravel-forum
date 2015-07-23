<?php namespace Riari\Forum\Repositories;

use Riari\Forum\Models\Post;

class Posts extends BaseRepository
{
    /**
     * Create a new post repository instance.
     *
     * @param  Post  $model
     */
    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    /**
     * Get N last posts belonging to the specified thread.
     *
     * @param  int  $threadID
     * @param  int  $count
     * @param  array  $with
     * @return Collection
     */
    public function getLastByThread($threadID = 0, $count = 10, array $with)
    {
        return $this->model->where('thread_id', $threadID)
            ->orderBy('created_at', 'DESC')
            ->take($count)
            ->with($with);
    }
}
