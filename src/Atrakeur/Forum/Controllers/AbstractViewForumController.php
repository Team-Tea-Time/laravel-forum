<?php namespace Atrakeur\Forum\Controllers;

use \Atrakeur\Forum\Models\ForumCategory;
use \Atrakeur\Forum\Models\ForumTopic;
use \Atrakeur\Forum\Models\ForumMessage;

class AbstractViewForumController extends AbstractForumController {

	private $categories;
	private $topics;

	public function __construct(ForumCategory $categories, ForumTopic $topics)
	{
		$this->categories = $categories;
		$this->topics     = $topics;
	}

	public function getIndex()
	{
		$categories = $this->categories->whereTopLevel()->with('subcategories')->get();

		$this->layout->content = \View::make('forum::index', compact('categories'));
	}

	public function getCategory($categoryId, $categoryUrl) 
	{
		$category = $this->categories->findOrFail($categoryId);

		$category->load('parentCategory', 'subCategories', 'topics');

		$parentCategory = $category->parentCategory;
		$subCategories  = $category->subCategories;
		$topics         = $category->topics;

		$this->layout->content = \View::make('forum::category', compact('parentCategory', 'category', 'subCategories', 'topics'));
	}

	public function getTopic($categoryId, $categoryUrl, $topicId, $topicUrl) 
	{
		$category       = $this->categories->findOrFail($categoryId);
		$parentCategory = $category->parentCategory;

		$topic    = $this->topics->findOrFail($topicId);
		$messages = $topic->messages()->paginate(\Config::get('forum::integration.messagesperpage'));

		$this->layout->content = \View::make('forum::topic', compact('parentCategory', 'category', 'topic', 'messages'));
	}

}
