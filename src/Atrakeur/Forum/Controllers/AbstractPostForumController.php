<?php namespace Atrakeur\Forum\Controllers;

use \Atrakeur\Forum\Models\ForumCategory;
use \Atrakeur\Forum\Models\ForumTopic;
use \Atrakeur\Forum\Models\ForumMessage;

abstract class AbstractPostForumController extends AbstractForumController {

	public function getNewTopic($categoryId, $categoryUrl)
	{
		$category       = ForumCategory::findOrFail($categoryId);
		$category->load('parentCategory');
		$parentCategory = $category->parentCategory;
		$actionUrl      = $category->postUrl;

		$this->layout->content = \View::make('forum::messageform', compact('parentCategory', 'category', 'actionUrl'));
	}

	public function postNewTopic($categoryId, $categoryUrl)
	{
		
	}

}
