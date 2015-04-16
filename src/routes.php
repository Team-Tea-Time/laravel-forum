<?php
if (!isset($root) || !isset($controller)) {
	throw new Exception ("This file can't be included outside of ForumServiceProvider@boot!");
}

Route::get($root, $controller . '@getViewIndex');
Route::group(['prefix' => $root], function() use ($new, $controller)
{
	$category = '{categoryID}-{categoryAlias}';
	$thread = '/{threadID}-{threadAlias}';

	Route::get('new', ['as' => 'forum.get.new', 'uses' => $controller . '@getViewNew']);
	Route::post('new/read', ['as' => 'forum.post.mark.read', 'uses' => $controller . '@postMarkAsRead']);

	Route::get($category, ['as' => 'forum.get.view.category', 'uses' => $controller . '@getViewCategory']);
	Route::get($category . $thread, ['as' => 'forum.get.view.thread', 'uses' => $controller . '@getViewThread']);

	Route::get($category . '/thread/create', ['as' => 'forum.get.create.thread', 'uses' => $controller . '@getCreateThread']);
	Route::post($category . '/thread/create', ['as' => 'forum.post.create.thread', 'uses' => $controller . '@postCreateThread']);

	Route::get($category . $thread . '/reply', ['as' => 'forum.get.reply.thread', 'uses' => $controller . '@getReplyToThread']);
	Route::post($category . $thread . '/reply', ['as' => 'forum.post.reply.thread', 'uses' => $controller . '@postReplyToThread']);

	Route::post($category . $thread . '/lock', ['as' => 'forum.post.lock.thread', 'uses' => $controller . '@postLockThread']);
	Route::post($category . $thread . '/pin', ['as' => 'forum.post.pin.thread', 'uses' => $controller . '@postPinThread']);
	Route::delete($category . $thread . '/delete', ['as' => 'forum.delete.thread', 'uses' => $controller . '@deleteThread']);

	Route::get($category . $thread . '/post/{postID}/edit', ['as' => 'forum.get.edit.post', 'uses' => $controller . '@getEditPost']);
	Route::post($category . $thread . '/post/{postID}/edit', ['as' => 'forum.post.edit.post', 'uses' => $controller . '@postEditPost']);
	Route::delete($category . $thread . '/post/{postID}/delete', ['as' => 'forum.get.delete.post', 'uses' => $controller . '@deletePost']);
});
