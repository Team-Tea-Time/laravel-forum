<?php namespace Atrakeur\Forum\Repositories;

use \Atrakeur\Forum\Models\ForumMessage;

class MessagesRepository extends AbstractBaseRepository {

	public function __construct(ForumMessage $model)
	{
		$this->model = $model;
	}

	public function getByTopic($topicId, array $with = array())
	{
		if (!is_numeric($topicId))
		{
			throw new \InvalidArgumentException();
		}

		return $this->getManyBy('parent_topic', $topicId);
	}

}
