<?php namespace Riari\Forum\Repositories;

use \Riari\Forum\Models\Category;

class Categories extends BaseRepository {

	public function __construct(Category $model)
	{
		$this->model = $model;
	}

	public function getByID($categoryID, array $with = array())
	{
		if (!is_numeric($categoryID))
		{
			throw new \InvalidArgumentException();
		}

		return $this->getFirstBy('id', $categoryID, $with);
	}

	public function getByParent($parent, array $with = array())
	{
		if (is_array($parent) && isset($parent['id']))
		{
			$parent = $parent['id'];
		}

		if ($parent != NULL && !is_numeric($parent))
		{
			throw new \InvalidArgumentException();
		}

		return $this->getManyBy('parent_category', $parent, $with);
	}

}
