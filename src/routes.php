<?php

// Forum index
get($root, ['as' => 'forum.index', 'uses' => "{$controllers['category']}@index"]);

Route::group(['prefix' => $root], function() use ($controllers)
{
	$category = '{category}-{categoryAlias}';
	$thread = '/{thread}-{threadAlias}';

	// New
	get('new', ['as' => 'forum.new.index', 'uses' => "{$controllers['thread']}@indexNew"]);
	patch('new/read', ['as' => 'forum.new.mark-read', 'uses' => "{$controllers['thread']}@markRead"]);

	// Categories
	get($category, ['as' => 'forum.category.index', 'uses' => "{$controllers['category']}@show"]);

	// Threads
	get($category . $thread, ['as' => 'forum.thread.show', 'uses' => "{$controllers['thread']}@show"]);
	get($category . '/thread/create', ['as' => 'forum.thread.create', 'uses' => "{$controllers['thread']}@create"]);
	post($category . '/thread/create', ['as' => 'forum.thread.store', 'uses' => "{$controllers['thread']}@store"]);
	patch($category . $thread . '/lock', ['as' => 'forum.thread.lock', 'uses' => "{$controllers['thread']}@lock"]);
	patch($category . $thread . '/pin', ['as' => 'forum.thread.pin', 'uses' => "{$controllers['thread']}@pin"]);
	delete($category . $thread . '/delete', ['as' => 'forum.thread.delete', 'uses' => "{$controllers['thread']}@destroy"]);

	// Posts
	get($category . $thread . '/reply', ['as' => 'forum.post.create', 'uses' => "{$controllers['post']}@create"]);
	post($category . $thread . '/reply', ['as' => 'forum.post.store', 'uses' => "{$controllers['post']}@store"]);
	get($category . $thread . '/post/{post}/edit', ['as' => 'forum.post.edit', 'uses' => "{$controllers['post']}@edit"]);
	patch($category . $thread . '/post/{post}/edit', ['as' => 'forum.post.update', 'uses' => "{$controllers['post']}@update"]);
	delete($category . $thread . '/post/{post}/delete', ['as' => 'forum.post.delete', 'uses' => "{$controllers['post']}@destroy"]);

	// API
	Route::group(['name' => 'api', 'prefix' => 'api/v1', 'namespace' => 'API\V1', 'middleware' => 'forum.auth.basic'], function()
	{
		resource('category', 'CategoryController', ['except' => ['create', 'edit']]);
		resource('thread', 'ThreadController', ['except' => ['create', 'edit']]);
		resource('post', 'PostController', ['except' => ['create', 'edit']]);
	});
});

// Model binding
Route::bind('category', function ($id)
{
	return bind_forum_parameter(new \Riari\Forum\Models\Category, $id);
});
Route::bind('thread', function ($id)
{
	return bind_forum_parameter(new \Riari\Forum\Models\Thread, $id);
});
Route::bind('post', function ($id)
{
	return bind_forum_parameter(new \Riari\Forum\Models\Post, $id);
});

function bind_forum_parameter($model, $id)
{
	if (\Riari\Forum\Support\Facades\Route::isAPI()) {
		return $model::find($id);
	}
	return $model::findOrFail($id);
}
