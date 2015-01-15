<?php namespace Eorzea\Forum\Repositories;

use Config;
use \Eorzea\Forum\Models\Post;

class Posts extends AbstractBaseRepository {

	public function __construct(Post $model)
	{
		$this->model = $model;

		$this->itemsPerPage = Config::get('forum::integration.posts_per_thread');
	}

	public function getByID($postID, Array $with = array())
	{
		if (!is_numeric($postID))
		{
			throw new \InvalidArgumentException();
		}

		return $this->getFirstBy('id', $postID, $with);
	}

	public function getByThread($threadID, Array $with = array())
	{
		if (!is_numeric($threadID))
		{
			throw new \InvalidArgumentException();
		}

		return $this->getManyBy('parent_thread', $threadID, $with);
	}

	public function getLastByThread($threadID, $count = 10, array $with = array())
	{
		if (!is_numeric($threadID))
		{
			throw new \InvalidArgumentException();
		}

		$model = $this->model->where('parent_thread', '=', $threadID);
		$model = $model->orderBy('created_at', 'DESC')->take($count);
		$model = $model->with($with);
		return $model;
	}

}
