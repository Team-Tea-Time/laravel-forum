<?php namespace Eorzea\Forum\Repositories;

use \Eorzea\Forum\Models\ForumPost;

class postsRepository extends AbstractBaseRepository {

	public function __construct(ForumPost $model)
	{
		$this->model = $model;
	}

	public function getById($postId, array $with = array())
	{
		if (!is_numeric($postId))
		{
			throw new \InvalidArgumentException();
		}

		return $this->getFirstBy('id', $postId, $with);
	}

	public function getByThread($threadId, array $with = array())
	{
		if (!is_numeric($threadId))
		{
			throw new \InvalidArgumentException();
		}

		return $this->getManyBy('parent_thread', $threadId, $with);
	}

	public function getLastByThread($threadId, $count = 10, array $with = array())
	{
		if (!is_numeric($threadId))
		{
			throw new \InvalidArgumentException();
		}

		$model = $this->model->where('parent_thread', '=', $threadId);
		$model = $model->orderBy('created_at', 'DESC')->take($count);
		$model = $model->with($with);
		return $this->model->convertToObject($model->get());
	}

}
