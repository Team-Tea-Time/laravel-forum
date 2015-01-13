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
	'user_model' => 'User',

	/*
	|--------------------------------------------------------------------------
	| Closure to determine the current user model
	|--------------------------------------------------------------------------
	|
	| Must return the current logged in user model to use
	| Non object, or NULL response is considered as not logged in
	|
	*/
	'current_user' => function() {
		//Here you can use confide facade,
		//or just the default facade, or whatever else

		return Auth::user();
	},

	/*
	|--------------------------------------------------------------------------
	| Application controller
	|--------------------------------------------------------------------------
	|
	| This class must extend \Eorzea\Forum\Controllers\AbstractController
	|
	*/
	'controller' => 'ForumController',

	/*
	|--------------------------------------------------------------------------
	| Pagination settings
	|--------------------------------------------------------------------------
	*/
	'threads_per_category' => 20,
	'posts_per_thread' => 15,
	'pagination_view' => ''

);
