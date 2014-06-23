<?php namespace Atrakeur\Forum\Controllers;

use Atrakeur\Forum\Repositories\CategoriesRepository;
use Atrakeur\Forum\Repositories\TopicsRepository;
use Atrakeur\Forum\Repositories\MessagesRepository;

abstract class AbstractPostForumController extends AbstractForumController {

	protected $topicRules = array(
			'title' => 'required',
	);

	protected $messageRules = array(
			'data' => 'required|min:5',
	);

	private $categories;
	private $topics;
	private $messages;

	public function __construct(CategoriesRepository $categories, TopicsRepository $topics, MessagesRepository $messages)
	{
		$this->categories = $categories;
		$this->topics     = $topics;
		$this->messages   = $messages;
	}

	public function getNewTopic($categoryId, $categoryUrl)
	{
		$user = $this->getCurrentUser();
		if ($user == NULL) 
		{
			return \App::abort(403, 'Access denied');
		}

		$category       = $this->categories->getById($categoryId, array('parentCategory'));
		$parentCategory = $category->parentCategory;
		$actionUrl      = $category->postUrl;

		$this->layout->content = \View::make('forum::post', compact('parentCategory', 'category', 'actionUrl'));
	}

	public function postNewTopic($categoryId, $categoryUrl)
	{
		$user = $this->getCurrentUser();
		if ($user == NULL) 
		{
			return \App::abort(403, 'Access denied');
		}

		$category  = $this->categories->getById($categoryId);
		$validator = \Validator::make(\Input::all(), array_merge($this->topicRules, $this->messageRules));
		if ($validator->passes())
		{
			$title = \Input::get('title');
			$data  = \Input::get('data');

			$topic                  = new \stdClass();			
			$topic->author          = $user->id;
			$topic->parent_category = $category->id;
			$topic->title           = $title;

			$this->fireEvent('forum.new.topic', array($topic));
			$topic = $this->topics->create($topic);
			$this->fireEvent('forum.saved.topic', array($topic));

			$message               = new \stdClass();
			$message->parent_topic = $topic->id;
			$message->author       = $user->id;
			$message->data         = $data;

			$this->fireEvent('forum.new.message', array($message));
			$message = $this->messages->create($message);
			$this->fireEvent('forum.saved.message', array($message));

			return \Redirect::to($topic->url)->with('success', 'topic created');
		}
		else 
		{
			return \Redirect::to($category->postUrl)->withErrors($validator)->withInput();
		}
	}

	public function getNewMessage($categoryId, $categoryUrl, $topicId, $topicUrl)
	{
		$user = $this->getCurrentUser();
		if ($user == NULL) 
		{
			return \App::abort(403, 'Access denied');
		}

		$category = $this->categories->getById($categoryId, array('parentCategory'));
		$topic    = $this->topics->getById($topicId);
		if ($category == NULL || $topic == NULL) 
		{
			return \App::abort(404);
		}

		$parentCategory = $category->parentCategory;
		$actionUrl      = $topic->postUrl;
		$prevMessages   = $this->messages->getLastByTopic($topicId);

		$this->layout->content = \View::make('forum::reply', compact('parentCategory', 'category', 'topic', 'actionUrl', 'prevMessages'));
	}

	public function postNewMessage($categoryId, $categoryUrl, $topicId, $topicUrl)
	{
		$user = $this->getCurrentUser();
		if ($user == NULL) 
		{
			return \App::abort(403, 'Access denied');
		}

		$category  = $this->categories->getById($categoryId);
		$topic     = $this->topics->getById($topicId);
		$validator = \Validator::make(\Input::all(), $this->messageRules);
		if ($validator->passes())
		{
			$data = \Input::get('data');

			$message               = new \stdClass();
			$message->parent_topic = $topic->id;
			$message->author       = $user->id;
			$message->data         = $data;

			$this->fireEvent('forum.new.message', array($message));
			$message = $this->messages->create($message);
			$this->fireEvent('forum.saved.message', array($message));

			return \Redirect::to($topic->url)->with('success', 'topic created');
		}
		else 
		{
			return \Redirect::to($topic->postUrl)->withErrors($validator)->withInput();
		}
	}

}
