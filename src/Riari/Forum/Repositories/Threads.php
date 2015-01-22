<?php namespace Riari\Forum\Repositories;

use Config;
use \Riari\Forum\Models\Thread;

class Threads extends BaseRepository {

	public function __construct(Thread $model)
	{
		$this->model = $model;

		$this->itemsPerPage = Config::get('forum::integration.threads_per_category');
	}

	public function getByID($threadID, Array $with = array())
	{
		if (!is_numeric($threadID))
		{
			throw new \InvalidArgumentException();
		}

		return $this->getFirstBy('id', $threadID, $with);
	}

}
