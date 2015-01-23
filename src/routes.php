<?php
if (!isset($root) || !isset($controller)) {
	throw new Exception ("This file can't be included outside of ForumServiceProvider@boot!");
}

Route::get($root, $controller . '@getViewIndex');
Route::group(['prefix' => $root], function() use ($controller)
{
	$category = '{categoryID}-{categoryAlias}';
	$thread = '/{threadID}-{threadAlias}';

	Route::get($category, ['as' => 'forum.get.view.category', 'uses' => $controller . '@getViewCategory']);
	Route::get($category . $thread, ['as' => 'forum.get.view.thread', 'uses' => $controller . '@getViewThread']);

	Route::get($category . '/thread/create', ['as' => 'forum.get.create.thread', 'uses' => $controller . '@getCreateThread']);
	Route::post($category . '/thread/create', ['as' => 'forum.post.create.thread', 'uses' => $controller . '@postCreateThread']);

	Route::get($category . $thread . '/reply', ['as' => 'forum.get.reply.thread', 'uses' => $controller . '@getReplyToThread']);
	Route::post($category . $thread . '/reply', ['as' => 'forum.post.reply.thread', 'uses' => $controller . '@postReplyToThread']);

	Route::get($category . $thread . '/lock', ['as' => 'forum.get.lock.thread', 'uses' => $controller . '@getLockThread']);
	Route::get($category . $thread . '/pin', ['as' => 'forum.get.pin.thread', 'uses' => $controller . '@getPinThread']);
	Route::get($category . $thread . '/delete', ['as' => 'forum.get.delete.thread', 'uses' => $controller . '@getDeleteThread']);

	Route::get($category . $thread . '/post/{postID}/edit', ['as' => 'forum.get.edit.post', 'uses' => $controller . '@getEditPost']);
	Route::post($category . $thread . '/post/{postID}/edit', ['as' => 'forum.post.edit.post', 'uses' => $controller . '@postEditPost']);

	Route::get($category . $thread . '/post/{postID}/delete', ['as' => 'forum.get.delete.post', 'uses' => $controller . '@getDeletePost']);
});
