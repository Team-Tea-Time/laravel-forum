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

}
