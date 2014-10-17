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

	protected function getCurrentUser()
	{
		$userfunc = \Config::get('forum::integration.currentuser');

		$user = $userfunc();
		if (is_object($user) && get_class($user) == \Config::get('forum::integration.usermodel'))
		{
			return $user;
		}

		return null;
	}

	protected function fireEvent($event, $data)
	{
		return \Event::fire($event, $data);
	}

}
