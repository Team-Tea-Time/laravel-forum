<?php namespace Eorzea\Forum\Controllers;

use Eorzea\Forum\Repositories\CategoriesRepository;
use Eorzea\Forum\Repositories\ThreadsRepository;
use Eorzea\Forum\Repositories\postsRepository;

class AbstractViewForumController extends AbstractForumController {

	private $categories;
	private $threads;
	private $posts;

	public function __construct(CategoriesRepository $categories, ThreadsRepository $threads, postsRepository $posts)
	{
		$this->categories = $categories;
		$this->threads     = $threads;
		$this->posts   = $posts;
	}

	public function getIndex()
	{
		$categories = $this->categories->getByParent(null, array('subcategories'));

		$this->layout->content = \View::make('forum::index', compact('categories'));
	}

	public function getCategory($categoryID, $categoryURL)
	{
		$category = $this->categories->getByID($categoryID, array('parentCategory', 'subCategories', 'threads'));
		if ($category == NULL)
		{
			return \App::abort(404);
		}

		$parentCategory = $category->parentCategory;
		$subCategories  = $category->subCategories;
		$threads         = $category->threads;

		$this->layout->content = \View::make('forum::category', compact('parentCategory', 'category', 'subCategories', 'threads'));

	}

	public function getThread($categoryID, $categoryURL, $threadID, $threadURL, $page = 0)
	{
		$category = $this->categories->getByID($categoryID, array('parentCategory'));
		if ($category == NULL)
		{
			return \App::abort(404);
		}

		$thread = $this->threads->getByID($threadID);
		if ($thread == NULL)
		{
			return \App::abort(404);
		}

		$parentCategory  = $category->parentCategory;
		$postsPerPage = \Config::get('forum::integration.postsperpage');
		//$this->posts->paginate($postsPerPage);
		$posts        = $this->posts->getByThread($thread->id, array('author'));
		$paginationLinks = $this->posts->getPaginationLinks($postsPerPage);

		$this->layout->content = \View::make('forum::thread', compact('parentCategory', 'category', 'thread', 'posts', 'paginationLinks'));
	}

}
