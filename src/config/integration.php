<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application user model
	|--------------------------------------------------------------------------
	|
	| The user model from the main application
	|
	*/
	'usermodel' => '\User',

	/*
	|--------------------------------------------------------------------------
	| Closure to determine the current user model
	|--------------------------------------------------------------------------
	|
	| Must return the current logged in user model to use
	| Non object, or NULL response is considered as not logged in
	|
	*/
	'currentuser' => function() {
		//Here you can use confide facade,
		//or just the default facade, or whatever else

		return Auth::user();
	},

	/*
	|--------------------------------------------------------------------------
	| Application forum view controller
	|--------------------------------------------------------------------------
	|
	| The controller used as application level hook for the forum (visualisation part)
	| This class must extend \Atrakeur\Forum\Controllers\AbstractViewForumController
	|
	*/
	'viewcontroller' => '\ForumController',

	/*
	|--------------------------------------------------------------------------
	| Application forum post controller
	|--------------------------------------------------------------------------
	|
	| The controller used as application level hook for the forum (post part)
	| This class must extend \Atrakeur\Forum\Controllers\AbstractPostForumController
	|
	*/
	'postcontroller' => '\ForumPostController',

	/*
	|--------------------------------------------------------------------------
	| The number of messages per page
	|--------------------------------------------------------------------------
	|
	*/
	'messagesperpage' => 15

);
