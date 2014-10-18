<?php namespace Atrakeur\Forum\Controllers;

use Atrakeur\Forum\Repositories\CategoriesRepository;
use Atrakeur\Forum\Repositories\TopicsRepository;
use Atrakeur\Forum\Repositories\MessagesRepository;

class AbstractViewForumController extends AbstractForumController {

	private $categories;
	private $topics;
	private $messages;

	public function __construct(CategoriesRepository $categories, TopicsRepository $topics, MessagesRepository $messages)
	{
		$this->categories = $categories;
		$this->topics     = $topics;
		$this->messages   = $messages;
	}

	public function getIndex()
	{
		$categories = $this->categories->getByParent(null, array('subcategories'));

		$this->layout->content = \View::make('forum::index', compact('categories'));
	}

	public function getCategory($categoryId, $categoryUrl)
	{
		$category = $this->categories->getById($categoryId, array('parentCategory', 'subCategories', 'topics'));
		if ($category == NULL)
		{
			return \App::abort(404);
		}

		$parentCategory = $category->parentCategory;
		$subCategories  = $category->subCategories;
		$topics         = $category->topics;

		$this->layout->content = \View::make('forum::category', compact('parentCategory', 'category', 'subCategories', 'topics'));

	}

	public function getTopic($categoryId, $categoryUrl, $topicId, $topicUrl, $page = 0)
	{
		$category = $this->categories->getById($categoryId, array('parentCategory'));
		if ($category == NULL)
		{
			return \App::abort(404);
		}

		$topic = $this->topics->getById($topicId);
		if ($topic == NULL)
		{
			return \App::abort(404);
		}

		$parentCategory  = $category->parentCategory;
		$messagesPerPage = \Config::get('forum::integration.messagesperpage');
		//$this->messages->paginate($messagesPerPage);
		$messages        = $this->messages->getByTopic($topic->id, array('author'));
		$paginationLinks = $this->messages->getPaginationLinks($messagesPerPage);

		$this->layout->content = \View::make('forum::topic', compact('parentCategory', 'category', 'topic', 'messages', 'paginationLinks'));
	}

}
