<?php namespace Atrakeur\Forum\Controllers;

use \Atrakeur\Forum\Models\ForumCategory;
use \Atrakeur\Forum\Models\ForumTopic;
use \Atrakeur\Forum\Models\ForumMessage;

abstract class AbstractPostForumController extends AbstractForumController {

	protected $topicRules = array(
			'title' => 'required',
	);

	protected $messageRules = array(
			'data' => 'required|min:5',
	);

	protected function getCurrentUser() 
	{
		$userfunc = \Config::get('forum::integration.currentuser');
		$user = $userfunc();
		return $user;
	}

	public function getNewTopic($categoryId, $categoryUrl)
	{
		$user = $this->getCurrentUser();
		if ($user == NULL) 
		{
			return \App::abort(403, 'Access denied');
		}

		$category       = ForumCategory::findOrFail($categoryId);
		$category->load('parentCategory');
		$parentCategory = $category->parentCategory;
		$actionUrl      = $category->postUrl;

		$this->layout->content = \View::make('forum::messageform', compact('parentCategory', 'category', 'actionUrl'));
	}

	public function postNewTopic($categoryId, $categoryUrl)
	{
		$user = $this->getCurrentUser();
		if ($user == NULL) 
		{
			return \App::abort(403, 'Access denied');
		}

		$category  = ForumCategory::findOrFail($categoryId);
		$validator = \Validator::make(\Input::all(), array_merge($this->topicRules, $this->messageRules));
		if ($validator->passes())
		{
			$title = \Input::get('title');
			$data  = \Input::get('data');

			$topic                  = new ForumTopic();
			$topic->parent_category = $category->id;
			$topic->author          = $user->id;
			$topic->title           = $title;
			$topic->save();

			$message               = new ForumMessage();
			$message->parent_topic = $topic->id;
			$message->author       = $user->id;
			$message->data         = $data;
			$message->save();

			return \Redirect::to($topic->url)->with('success', 'topic created');
		}
		else 
		{
			return \Redirect::to($category->url)->withErrors($validator);
		}
	}

}
