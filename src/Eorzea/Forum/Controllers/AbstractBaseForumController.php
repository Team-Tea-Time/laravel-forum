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
		$user_callback = Config::get('forum::integration.current_user');

		$user = $user_callback();
		if (is_object($user) && get_class($user) == Config::get('forum::integration.user_model'))
		{
			return $user;
		}

		return NULL;
	}

}
