<?php namespace Atrakeur\Forum\Repositories;

use \Atrakeur\Forum\Models\ForumTopic;

class TopicsRepository extends AbstractBaseRepository {

	public function __construct(ForumTopic $model)
	{
		$this->model = $model;
	}

	public function getById($ident, array $with = array())
	{
		if (!is_numeric($ident))
		{
			throw new \InvalidArgumentException();
		}

		return $this->getFirstBy('id', $ident, $with);
	}

}
