<?php namespace Eorzea\Forum\Controllers;

use \Eorzea\Forum\Models\ForumCategory;
use \Eorzea\Forum\Models\ForumThread;
use \Eorzea\Forum\Models\ForumPost;

abstract class AbstractForumController extends Controller {

	protected $layout = 'forum::layouts.master';

	protected function setupLayout()
	{
		if ($this->layout != NULL)
		{
			$this->layout = View::make($this->layout);
		}
	}

	protected function getCurrentUser()
	{
		$userfunc = Config::get('forum::integration.currentuser');

		$user = $userfunc();
		if (is_object($user) && get_class($user) == Config::get('forum::integration.usermodel'))
		{
			return $user;
		}

		return null;
	}

	protected function userCan($permission)
	{
		// Fetch the current user
		$user = Config::get('forum::integration.currentuser')();

		// Check for access permission
		$access_granted = Config::get('forum::access_forums')($this, $user);

		if (!$access_granted)
		{
			return FALSE;
		}

		// Check for action permission
		$action_granted = Config::get('forum::' . $permission)($this, $user);

		return $access_granted;
	}

}
