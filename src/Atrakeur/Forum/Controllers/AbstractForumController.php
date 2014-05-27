<?php namespace Atrakeur\Forum\Controllers;

use \Atrakeur\Forum\Models\ForumCategory;
use \Atrakeur\Forum\Models\ForumTopic;
use \Atrakeur\Forum\Models\ForumMessage;

abstract class AbstractForumController extends \Controller {

	protected $layout = 'forum::layouts.master';

	protected function setupLayout()
	{
		if ($this->layout != NULL)
		{
			$this->layout = \View::make($this->layout);
		}
	}

	public function getIndex()
	{
		$categories = ForumCategory::whereTopLevel()->with('subcategories')->get();

		$this->layout->content = \View::make('forum::index', compact('categories'));
	}

	public function getCategory($categoryId, $categoryUrl) 
	{
		$category = ForumCategory::findOrFail($categoryId);

		$category->load('parentCategory', 'subCategories', 'topics');

		$parentCategory = $category->parentCategory;
		$subCategories  = $category->subCategories;
		$topics         = $category->topics;

		$this->layout->content = \View::make('forum::category', compact('parentCategory', 'category', 'subCategories', 'topics'));
	}

	public function getTopic($categoryId, $categoryUrl, $topicId, $topicUrl) 
	{
		$category       = ForumCategory::findOrFail($categoryId);
		$parentCategory = $category->parentCategory;

		$topic    = ForumTopic::findOrFail($topicId);
		$messages = $topic->messages()->paginate(15);

		$this->layout->content = \View::make('forum::topic', compact('parentCategory', 'category', 'topic', 'messages'));
	}

}
