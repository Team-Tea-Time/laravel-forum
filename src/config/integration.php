<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Application controllers
	|--------------------------------------------------------------------------
	|
	| Here we specify which controllers to use for each component of the forum.
	| You can optionally extend these controllers and change the namespaces
	| here to reference your custom versions instead.
	|
	*/

	'controllers' => [
		'category'	=> '\Riari\Forum\Http\Controllers\CategoryController',
		'thread'	=> '\Riari\Forum\Http\Controllers\ThreadController',
		'post'		=> '\Riari\Forum\Http\Controllers\PostController'
	],

	/*
	|--------------------------------------------------------------------------
	| Application models
	|--------------------------------------------------------------------------
	|
	| Here we specify models in your application that the default forum
	| integration depends on. Currently only the user model is used.
	|
	*/

	'models' => [
		'user'	=> 'App\User'
	],

	/*
	|--------------------------------------------------------------------------
	| Application user
	|--------------------------------------------------------------------------
	|
	| Here we specify some settings related to the application user. Currently
	| only the names of attributes used by the forum are specified here.
	|
	*/

	'user' => [
		'attributes' => [
			'id'	=> 'id',
			'name'	=> 'name'
		]
	],

	/*
	|--------------------------------------------------------------------------
	| Closure: determine the current user model
	|--------------------------------------------------------------------------
	|
	| Must return the model of the currently logged in user, or null if the
	| user is a guest.
	|
	*/

	'current_user' => function ()
	{
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
	| $type: The type of alert. One of 'success' or 'danger'.
	| $message: The alert message.
	|
	*/

	'process_alert' => function ($type, $message)
	{
		$alerts = [];
		if (Session::has('alerts')) {
			$alerts = Session::get('alerts');
		}

		Session::flash('alerts', array_merge($alerts, [['type' => $type, 'message' => $message]]));
	},

	/*
	|--------------------------------------------------------------------------
	| Closure: process permission denied
	|--------------------------------------------------------------------------
	|
	| Use this to control what happens when a permission is denied to a user.
	| Note this does not affect inline permission checks for displaying links
	| or inputs.
	|
	| $context: The model related to the permission being checked.
	| $user: The current user.
	|
	*/

	'process_denied' => function ($context, $user)
	{
		App::abort(403);
	},

];
