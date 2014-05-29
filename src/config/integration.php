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
	| Application forum controller
	|--------------------------------------------------------------------------
	|
	| The controller used as application level hook for the forum
	| This class must extend \Atrakeur\Forum\Controllers\ForumController
	|
	*/
	'forumcontroller' => '\ForumController'

);
