<?php namespace Atrakeur\Forum\Controllers;

use \Atrakeur\Forum\Models\ForumCategory;

abstract class ForumController extends \Controller {

	public function index()
	{
		var_dump(ForumCategory::whereTopLevel()->with('subcategories')->get()->toArray());
	}

}
