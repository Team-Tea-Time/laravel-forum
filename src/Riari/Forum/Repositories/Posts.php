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
        $this->perPage = config('forum.preferences.pagination.posts');
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
        $model = $this->model->where('thread_id', $threadID);
        $model = $model->orderBy('created_at', 'DESC')->take($count);

        return $model->with($with);

        return $this->model->where('thread_id', $threadID)
            ->orderBy('created_at', 'DESC')
            ->take($count)
            ->with($with);
    }
}
