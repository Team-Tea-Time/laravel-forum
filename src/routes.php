<?php
if (!isset($root) || !isset($controller)) {
	throw new Exception ("This file can't be included outside of ForumServiceProvider@boot!");
}

Route::get($root, $controller . '@getIndex');
Route::group(['prefix' => $root], function() use ($controller)
{
	$category = '{categoryID}-{categoryAlias}';
	$thread = '/{threadID}-{threadAlias}';

	Route::get($category, ['as' => 'forum.get.category', 'uses' => $controller . '@getCategory']);
	Route::get($category . $thread, ['as' => 'forum.get.thread', 'uses' => $controller . '@getThread']);

	Route::get($category . '/thread/create', ['as' => 'forum.get.create.thread', 'uses' => $controller . '@getCreateThread']);
	Route::post($category . '/thread/create', ['as' => 'forum.post.create.thread', 'uses' => $controller . '@postCreateThread']);

	Route::get($category . $thread . '/post/create', ['as' => 'forum.get.create.post', 'uses' => $controller . '@getCreatePost']);
	Route::post($category . $thread . '/post/create', ['as' => 'forum.post.create.post', 'uses' => $controller . '@postCreatePost']);

	Route::get($category . $thread . '/post/{postID}/edit', ['as' => 'forum.get.edit.post', 'uses' => $controller . '@getEditPost']);
	Route::post($category . $thread . '/post/{postID}/edit', ['as' => 'forum.post.edit.post', 'uses' => $controller . '@postEditPost']);
});
