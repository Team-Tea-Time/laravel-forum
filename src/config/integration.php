<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Application user model
	|--------------------------------------------------------------------------
	|
	| The user model from the main application
	|
	*/

	'user_model' => 'App\User',

	/*
	|--------------------------------------------------------------------------
	| Application user name attribute
	|--------------------------------------------------------------------------
	|
	| The attribute on the user model to use as a display name.
	|
	*/

	'user_name_attribute' => 'name',

	/*
	|--------------------------------------------------------------------------
	| Closure: determine the current user model
	|--------------------------------------------------------------------------
	|
	| Must return the current logged in user model to use
	| Non object, or null response is considered as not logged in
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
		$alerts = array();
		if (Session::has('alerts'))
		{
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
	*/

	'process_denied' => function($context, $user) {
		App::abort(403);
	},

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
	]

];
