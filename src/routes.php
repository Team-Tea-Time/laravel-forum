<?php
if (!isset($root) || !isset($controller)) {
	throw new Exception ("This file can't be included outside of ForumServiceProvider@boot!");
}

Route::get($root, $controller . '@getIndex');
Route::group(array('prefix' => $root), function() use ($controller)
{
	$category = '{categoryID}-{categoryAlias}';
	$thread = '/{threadID}-{threadAlias}';

	Route::get($category, array('as' => 'forum.get.category', 'uses' => $controller . '@getCategory'));
	Route::get($category . $thread, array('as' => 'forum.get.thread', 'uses' => $controller . '@getThread'));

	Route::get($category . '/thread/create', array('as' => 'forum.get.create.thread', 'uses' => $controller . '@getCreateThread'));
	Route::post($category . '/thread/create', array('as' => 'forum.post.create.thread', 'uses' => $controller . '@postCreateThread'));

	Route::get($category . $thread . '/post/create', array('as' => 'forum.get.create.post', 'uses' => $controller . '@getCreatePost'));
	Route::post($category . $thread . '/post/create', array('as' => 'forum.post.create.post', 'uses' => $controller . '@postCreatePost'));

	Route::get($category . $thread . '/post/{postID}/edit', array('as' => 'forum.get.edit.post', 'uses' => $controller . '@getEditPost'));
	Route::post($category . $thread . '/post/{postID}/edit', array('as' => 'forum.post.edit.post', 'uses' => $controller . '@postEditPost'));
});
