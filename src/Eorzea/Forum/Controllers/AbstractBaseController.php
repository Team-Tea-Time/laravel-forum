<?php namespace Eorzea\Forum\Controllers;

use Controller;
use Config;
use View;

abstract class AbstractBaseController extends Controller {

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
