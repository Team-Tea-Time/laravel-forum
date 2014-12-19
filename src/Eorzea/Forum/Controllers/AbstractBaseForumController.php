<?php namespace Eorzea\Forum\Controllers;

use Eorzea\Forum\Models\ForumCategory;
use Eorzea\Forum\Models\ForumThread;
use Eorzea\Forum\Models\ForumPost;

use Controller;
use Config;
use View;

abstract class AbstractBaseForumController extends Controller {

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
		$user_callback = Config::get('forum::integration.currentuser');

		$user = $user_callback();
		if (is_object($user) && get_class($user) == Config::get('forum::integration.usermodel'))
		{
			return $user;
		}

		return NULL;
	}

	protected function userCan($permission)
	{
		// Fetch the current user
		$user_callback = Config::get('forum::integration.currentuser');
		$user = $user_callback();

		// Check for access permission
		$access_callback = Config::get('forum::access_forums');
		$permission_granted = $access_callback($this, $user);

		if ( $permission_granted && ( $permission != 'access_forums' ) )
		{
			// Check for action permission
			$action_callback = Config::get('forum::' . $permission);
			$permission_granted = $action_callback($this, $user);
		}

		return $permission_granted;
	}

}
