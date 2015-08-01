<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Controllers
	|--------------------------------------------------------------------------
	|
	| Here we specify the namespace and controllers to use. Change these if
	| you want to extend the provided classes and use your own instead.
	|
	*/

	'controllers' => [
		'namespace'	=> 'Riari\Forum\Http\Controllers',
		'category'	=> 'CategoryController',
		'thread'	=> 'ThreadController',
		'post'		=> 'PostController'
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
		'user'	=> App\User::class
	],

	/*
	|--------------------------------------------------------------------------
	| Application user
	|--------------------------------------------------------------------------
	|
	| Here we specify some settings related to the application user.
	|
	*/

	'user' => [
		'attributes' => [
			'name' => 'name'
		]
	],

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

	/**
	 * @param  string  $type	The type of alert ('success' or 'danger')
	 * @param  string  $message The alert message
	 */
	'process_alert' => function ($type, $message)
	{
		$alerts = [];
		if (Session::has('alerts')) {
			$alerts = Session::get('alerts');
		}

		Session::flash('alerts', array_merge($alerts, [['type' => $type, 'message' => $message]]));
	},

];
