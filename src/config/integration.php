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
	| Application forum controller
	|--------------------------------------------------------------------------
	|
	| The controller used as application level hook for the forum
	| This class must extend \Atrakeur\Forum\Controllers\ForumController
	|
	*/
	'forumcontroller' => '\ForumController'

);
