<?php namespace Eorzea\Forum\Controllers;

use Eorzea\Forum\Repositories\CategoriesRepository;
use Eorzea\Forum\Repositories\ThreadsRepository;
use Eorzea\Forum\Repositories\PostsRepository;

abstract class AbstractPostForumController extends AbstractForumController {

	protected $threadRules = array(
			'title' => 'required',
	);

	protected $postRules = array(
			'data' => 'required|min:5',
	);

	private $categories;
	private $threads;
	private $posts;

	public function __construct(CategoriesRepository $categories, ThreadsRepository $threads, PostsRepository $posts)
	{
		$this->categories = $categories;
		$this->threads     = $threads;
		$this->posts   = $posts;
	}

	public function getNewThread($categoryID, $categoryURL)
	{
		$user = $this->getCurrentUser();
		if ($user == NULL)
		{
			return \App::abort(403, 'Access denied');
		}

		$category       = $this->categories->getByID($categoryID, array('parentCategory'));
		$parentCategory = $category->parentCategory;
		$actionURL      = $category->postURL;

		$this->layout->content = \View::make('forum::post', compact('parentCategory', 'category', 'actionURL'));
	}

	public function postNewThread($categoryID, $categoryURL)
	{
		if (!$this->userCan('create_threads'))
		{
			return \App::abort(403, 'Access denied');
		}

		$category  = $this->categories->getByID($categoryID);
		$validator = \Validator::make(\Input::all(), array_merge($this->threadRules, $this->postRules));
		if ($validator->passes())
		{
			$title = \Input::get('title');
			$data  = \Input::get('data');

			$thread                  = new \stdClass();
			$thread->author_id       = $user->id;
			$thread->parent_category = $category->id;
			$thread->title           = $title;

			$this->fireEvent('forum.new.thread', array($thread));
			$thread = $this->threads->create($thread);
			$this->fireEvent('forum.saved.thread', array($thread));

			$post               = new \stdClass();
			$post->parent_thread = $thread->id;
			$post->author_id    = $user->id;
			$post->data         = $data;

			$this->fireEvent('forum.new.post', array($post));
			$post = $this->posts->create($post);
			$this->fireEvent('forum.saved.post', array($post));

			return \Redirect::to($thread->url)->with('success', 'thread created');
		}
		else
		{
			return \Redirect::to($category->postURL)->withErrors($validator)->withInput();
		}
	}

	public function getNewPost($categoryID, $categoryURL, $threadID, $threadURL)
	{
		$user = $this->getCurrentUser();
		if ($user == NULL)
		{
			return \App::abort(403, 'Access denied');
		}

		$category = $this->categories->getByID($categoryID, array('parentCategory'));
		$thread    = $this->threads->getByID($threadID);
		if ($category == NULL || $thread == NULL)
		{
			return \App::abort(404);
		}

		$parentCategory = $category->parentCategory;
		$actionURL      = $thread->postURL;
		$prevPosts   = $this->posts->getLastByThread($threadID);

		$this->layout->content = \View::make('forum::reply', compact('parentCategory', 'category', 'thread', 'actionURL', 'prevPosts'));
	}

	public function postNewPost($categoryID, $categoryURL, $threadID, $threadURL)
	{
		if (!$this->userCan('post_replies'))
		{
			return \App::abort(403, 'Access denied');
		}

		$category  = $this->categories->getByID($categoryID);
		$thread     = $this->threads->getByID($threadID);
		$validator = \Validator::make(\Input::all(), $this->postRules);
		if ($validator->passes())
		{
			$data = \Input::get('data');

			$post               = new \stdClass();
			$post->parent_thread = $thread->id;
			$post->author_id    = $user->id;
			$post->data         = $data;

			$this->fireEvent('forum.new.post', array($post));
			$post = $this->posts->create($post);
			$this->fireEvent('forum.saved.post', array($post));

			return \Redirect::to($thread->url)->with('success', 'thread created');
		}
		else
		{
			return \Redirect::to($thread->postURL)->withErrors($validator)->withInput();
		}
	}

	public function getEditPost($categoryID, $categoryURL, $threadID, $threadURL, $postID)
	{
		$user = $this->getCurrentUser();
		if ($user == NULL)
		{
			return \App::abort(403, 'Access denied');
		}

		$category = $this->categories->getByID($categoryID, array('parentCategory'));
		$thread    = $this->threads->getByID($threadID);
		$post  = $this->posts->getByID($postID);
		if ($category == NULL || $thread == NULL || $post == NULL)
		{
			return \App::abort(404);
		}

		$parentCategory = $category->parentCategory;
		$actionURL      = $post->postURL;

		$this->layout->content = \View::make('forum::edit', compact('parentCategory', 'category', 'thread', 'post', 'actionURL'));
	}

	public function postEditPost($categoryID, $categoryURL, $threadID, $threadURL, $postID)
	{
		if (!$this->userCan('edit_posts'))
		{
			return \App::abort(403, 'Access denied');
		}

		$category = $this->categories->getByID($categoryID, array('parentCategory'));
		$thread    = $this->threads->getByID($threadID);
		$post  = $this->posts->getByID($postID);
		if ($category == NULL || $thread == NULL || $post == NULL)
		{
			return \App::abort(404);
		}

		$validator = \Validator::make(\Input::all(), $this->postRules);
		if ($validator->passes())
		{
			$data = \Input::get('data');

			$post               = new \stdClass();
			$post->id           = $postID;
			$post->parent_thread = $thread->id;
			$post->author_id    = $user->id;
			$post->data         = $data;

			$this->fireEvent('forum.new.post', array($post));
			$post = $this->posts->update($post);
			$this->fireEvent('forum.saved.post', array($post));

			return \Redirect::to($post->url)->with('success', 'thread created');
		}
		else
		{
			return \Redirect::to($post->postURL)->withErrors($validator)->withInput();
		}
	}

}
