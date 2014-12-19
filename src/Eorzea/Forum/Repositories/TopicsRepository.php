<?php namespace Eorzea\Forum\Repositories;

use \Eorzea\Forum\Models\ForumThread;

class ThreadsRepository extends AbstractBaseRepository {

	public function __construct(ForumThread $model)
	{
		$this->model = $model;
	}

	public function getByID($ident, array $with = array())
	{
		if (!is_numeric($ident))
		{
			throw new InvalidArgumentException();
		}

		return $this->getFirstBy('id', $ident, $with);
	}

}
