<?php namespace Atrakeur\Forum\Repositories;

use \Atrakeur\Forum\Models\ForumMessage;

class MessagesRepository extends AbstractBaseRepository {

	public function __construct(ForumMessage $model)
	{
		$this->model = $model;
	}

	public function getById($messageId, array $with = array())
	{
		if (!is_numeric($messageId))
		{
			throw new \InvalidArgumentException();
		}

		return $this->getFirstBy('id', $messageId, $with);
	}

	public function getByTopic($topicId, array $with = array())
	{
		if (!is_numeric($topicId))
		{
			throw new \InvalidArgumentException();
		}

		return $this->getManyBy('parent_topic', $topicId, $with);
	}

	public function getLastByTopic($topicId, $count = 10, array $with = array())
	{
		if (!is_numeric($topicId))
		{
			throw new \InvalidArgumentException();
		}

		$model = $this->model->where('parent_topic', '=', $topicId);
		$model = $model->orderBy('created_at', 'DESC')->take($count);
		$model = $model->with($with);
		return $this->model->convertToObject($model->get());
	}

}
