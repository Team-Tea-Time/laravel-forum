<?php
if (!isset($root) || !isset($controller)) {
	throw new Exception ("This file can't be included outside of ForumServiceProvider@boot!");
}

$category = $root . '{categoryID}-{categoryAlias}';
$thread = '/{threadID}-{threadAlias}';

Route::get($root, $controller . '@getIndex');
Route::get($root . $category, $controller . '@getCategory');
Route::get($root . $category . '/{threadID}-{threadAlias}', $controller . '@getThread');

Route::get($root . $category . '/create', $controller . '@getCreateThread');
Route::post($root . $category . '/create', $controller . '@postCreateThread');

Route::get($root . $category . $thread . '/create', $controller . '@getCreatePost');
Route::post($root . $category . $thread . '/create', $controller . '@postCreatePost');

Route::get($root . $category . $thread . '/edit/{postID}', $controller . '@getEditPost');
Route::post($root . $category . $thread . '/edit/{postID}', $controller . '@postEditPost');
