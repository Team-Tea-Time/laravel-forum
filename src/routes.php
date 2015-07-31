<?php

// Forum index
get($root, ['as' => 'forum.index', 'uses' => "{$controllers['category']}@index"]);

Route::group(['prefix' => $root], function () use ($parameters, $controllers)
{
	$category = "{{$parameters['category']}}-{category_slug}";
	$thread = "{{$parameters['thread']}}-{thread_slug}";

	// New
	get('new', ['as' => 'forum.new.index', 'uses' => "{$controllers['thread']}@indexNew"]);
	patch('new/read', ['as' => 'forum.new.mark-read', 'uses' => "{$controllers['thread']}@markRead"]);

	// Categories
	get($category, ['as' => 'forum.category.index', 'uses' => "{$controllers['category']}@show"]);

	// Threads
	get("{$category}/{$thread}", ['as' => 'forum.thread.show', 'uses' => "{$controllers['thread']}@show"]);
	get("{$category}/{$thread}/thread/create", ['as' => 'forum.thread.create', 'uses' => "{$controllers['thread']}@create"]);
	post("{$category}/{$thread}/thread/create", ['as' => 'forum.thread.store', 'uses' => "{$controllers['thread']}@store"]);

	// Posts
	get("{$category}/{$thread}/post/{{$parameters['post']}}", ['as' => 'forum.post.show', 'uses' => "{$controllers['post']}@show"]);
	get("{$category}/{$thread}/reply", ['as' => 'forum.post.create', 'uses' => "{$controllers['post']}@create"]);
	post("{$category}/{$thread}/reply", ['as' => 'forum.post.store', 'uses' => "{$controllers['post']}@store"]);
	get("{$category}/{$thread}/post/{{$parameters['post']}}/edit", ['as' => 'forum.post.edit', 'uses' => "{$controllers['post']}@edit"]);
	patch($category . $thread . "/post/{{$parameters['post']}}/edit", ['as' => 'forum.post.update', 'uses' => "{$controllers['post']}@update"]);

	// API
	Route::group(['name' => 'api', 'prefix' => 'api/v1', 'namespace' => 'API\V1', 'middleware' => 'forum.auth.basic'], function () use ($parameters)
	{
		// Categories
		resource($parameters['category'], 'CategoryController', ['except' => ['create', 'edit']]);
		patch("category/{{$parameters['category']}}/restore", ['as' => 'forum.api.v1.category.restore', 'uses' => 'CategoryController@restore']);

		// Threads
		resource($parameters['thread'], 'ThreadController', ['except' => ['create', 'edit']]);
		patch("thread/{{$parameters['thread']}}/restore", ['as' => 'forum.api.v1.thread.restore', 'uses' => 'ThreadController@restore']);

		// Posts
		resource($parameters['post'], 'PostController', ['except' => ['create', 'edit']]);
		patch("post/{{$parameters['thread']}}/restore", ['as' => 'forum.api.v1.post.restore', 'uses' => 'PostController@restore']);

		Route::group(['prefix' => 'bulk'], function ()
		{
			// Threads
			patch('thread/lock', ['as' => 'forum.api.v1.bulk.thread.lock', 'uses' => 'ThreadController@bulkLock']);
			patch('thread/pin', ['as' => 'forum.api.v1.bulk.thread.pin', 'uses' => 'ThreadController@bulkPin']);
			patch('thread/move', ['as' => 'forum.api.v1.bulk.thread.move', 'uses' => 'ThreadController@bulkMove']);
			delete('thread/delete', ['as' => 'forum.api.v1.bulk.thread.destroy', 'uses' => 'ThreadController@bulkDestroy']);
			patch('thread/restore', ['as' => 'forum.api.v1.bulk.thread.restore', 'uses' => 'ThreadController@bulkRestore']);

			// Posts
			delete('post/delete', ['as' => 'forum.api.v1.bulk.post.destroy', 'uses' => 'PostController@bulkDestroy']);
			patch('post/restore', ['as' => 'forum.api.v1.bulk.post.restore', 'uses' => 'PostController@bulkRestore']);
		});
	});
});

// Model binding
Route::bind($parameters['category'], function ($id)
{
	return bind_forum_parameter(new \Riari\Forum\Models\Category, $id);
});
Route::bind($parameters['thread'], function ($id)
{
	return bind_forum_parameter(new \Riari\Forum\Models\Thread, $id);
});
Route::bind($parameters['post'], function ($id)
{
	return bind_forum_parameter(new \Riari\Forum\Models\Post, $id);
});

function bind_forum_parameter($model, $id)
{
	if (\Riari\Forum\Support\Facades\Route::isAPI()) {
		return $model->find($id);
	}
	return $model->findOrFail($id);
}
