<?php namespace Atrakeur\Forum\Controllers;

use \Atrakeur\Forum\Models\ForumCategory;

abstract class AbstractForumController extends \Controller {

	public function getIndex()
	{
		$categories = ForumCategory::whereTopLevel()->with('subcategories')->get();

		return \View::make('forum::index', compact('categories'));
	}

	public function getCategory($categoryId, $categoryUrl) 
	{
		echo $categoryId.' / '.$categoryUrl;
	}

}
