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
	| Closure: determine the current user model
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
	| Closure: process alert messages
	|--------------------------------------------------------------------------
	|
	| Change this if your app has its own user alert/notification system.
	| NOTE: remember to override the forum views to remove the default alerts
	| if you no longer use them.
	|
	*/
	'process_alert' => function($type, $message) {
		View::share('alerts', [$type => $message]);
	},

	/*
	|--------------------------------------------------------------------------
	| Application controller
	|--------------------------------------------------------------------------
	|
	| This class must extend \Riari\Forum\Controllers\AbstractBaseController
	|
	*/
	'controller' => 'ForumController'

);
