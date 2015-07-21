<?php

// Forum index
Route::get($root, ['as' => 'forum.index', 'uses' => "{$controllers['category']}@index"]);
Route::group(['prefix' => $root], function() use ($controllers)
{
	$category = '{category}-{categoryAlias}';
	$thread = '/{thread}-{threadAlias}';

	// New
	Route::get('new', ['as' => 'forum.new.index', 'uses' => "{$controllers['thread']}@new"]);
	Route::post('new/read', ['as' => 'forum.new.mark-read', 'uses' => "{$controllers['thread']}@markRead"]);

	// Categories
	Route::get($category, ['as' => 'forum.category.index', 'uses' => "{$controllers['category']}@show"]);

	// Threads
	Route::get($category . $thread, ['as' => 'forum.thread.show', 'uses' => "{$controllers['thread']}@show"]);
	Route::get($category . '/thread/create', ['as' => 'forum.thread.create', 'uses' => "{$controllers['thread']}@create"]);
	Route::post($category . '/thread/create', ['as' => 'forum.thread.store', 'uses' => "{$controllers['thread']}@store"]);
	Route::post($category . $thread . '/lock', ['as' => 'forum.thread.lock', 'uses' => "{$controllers['thread']}@lock"]);
	Route::post($category . $thread . '/pin', ['as' => 'forum.thread.pin', 'uses' => "{$controllers['thread']}@pin"]);
	Route::delete($category . $thread . '/delete', ['as' => 'forum.thread.delete', 'uses' => "{$controllers['thread']}@destroy"]);

	// Posts
	Route::get($category . $thread . '/reply', ['as' => 'forum.post.create', 'uses' => "{$controllers['post']}@create"]);
	Route::post($category . $thread . '/reply', ['as' => 'forum.post.store', 'uses' => "{$controllers['post']}@store"]);
	Route::get($category . $thread . '/post/{post}/edit', ['as' => 'forum.post.edit', 'uses' => "{$controllers['post']}@edit"]);
	Route::patch($category . $thread . '/post/{post}/edit', ['as' => 'forum.post.update', 'uses' => "{$controllers['post']}@update"]);
	Route::delete($category . $thread . '/post/{post}/delete', ['as' => 'forum.post.delete', 'uses' => "{$controllers['post']}@destroy"]);

	// API
	Route::group(['prefix' => 'api/v1', 'namespace' => 'API\V1', 'middleware' => 'forum.auth.basic'], function()
	{
		Route::resource('category', 'CategoryController');
		Route::resource('thread', 'ThreadController');
		Route::resource('post', 'PostController');
	});
});

// Model binding
Route::model('category', 'Riari\Forum\Models\Category');
Route::model('thread', 'Riari\Forum\Models\Thread');
Route::model('post', 'Riari\Forum\Models\Post');
