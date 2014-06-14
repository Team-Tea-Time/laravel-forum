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

			$this->fireEvent('forum.new.topic', array($topic));
			$topic->save();
			$this->fireEvent('forum.saved.topic', array($topic));

			$message               = new ForumMessage();
			$message->parent_topic = $topic->id;
			$message->author       = $user->id;
			$message->data         = $data;

			$this->fireEvent('forum.new.message', array($message));
			$message->save();
			$this->fireEvent('forum.saved.message', array($message));

			return \Redirect::to($topic->url)->with('success', 'topic created');
		}
		else 
		{
			return \Redirect::to($category->url)->withErrors($validator);
		}
	}

	public function getNewMessage($categoryId, $categoryUrl, $topicId, $topicUrl)
	{
		$user = $this->getCurrentUser();
		if ($user == NULL) 
		{
			return \App::abort(403, 'Access denied');
		}

		$category       = ForumCategory::findOrFail($categoryId);
		$category->load('parentCategory');
		$parentCategory = $category->parentCategory;
		$topic          = ForumTopic::findORFail($topicId);
		$actionUrl      = $topic->postUrl;
		$prevMessages   = $topic->messages()->orderBy('id', 'DESC')->take(10)->get();

		$this->layout->content = \View::make('forum::messageform', compact('parentCategory', 'category', 'topic', 'actionUrl', 'prevMessages'));
	}

}
