<?php namespace Eorzea\Forum\Repositories;

use \Eorzea\Forum\Models\Post;

class Posts extends AbstractBaseRepository {

	public function __construct(Post $model)
	{
		$this->model = $model;
	}

	public function getById($postID, Array $with = array())
	{
		if (!is_numeric($postID))
		{
			throw new InvalidArgumentException();
		}

		return $this->getFirstBy('id', $postID, $with);
	}

	public function getByThread($threadID, Array $with = array())
	{
		if (!is_numeric($threadID))
		{
			throw new InvalidArgumentException();
		}

		return $this->getManyBy('parent_thread', $threadID, $with);
	}

	public function getLastByThread($threadID, $count = 10, array $with = array())
	{
		if (!is_numeric($threadID))
		{
			throw new InvalidArgumentException();
		}

		$model = $this->model->where('parent_thread', '=', $threadID);
		$model = $model->orderBy('created_at', 'DESC')->take($count);
		$model = $model->with($with);
		return $this->model->convertToObject($model->get());
	}

}
