<?php

// Forum index
$r->get('/', ['as' => 'index', 'uses' => 'CategoryController@index']);

// New/updated threads
$r->get('new', ['as' => 'new.index', 'uses' => 'ThreadController@indexNew']);
$r->patch('new', ['as' => 'new.mark-read', 'uses' => 'ThreadController@markNewAsRead']);

// Categories
$r->post('category/create', ['as' => 'category.store', 'uses' => 'CategoryController@store']);
$r->group(['prefix' => '{category}-{category_slug}'], function ($r) {
    $r->get('/', ['as' => 'category.show', 'uses' => 'CategoryController@show']);
    $r->patch('/', ['as' => 'category.update', 'uses' => 'CategoryController@update']);
    $r->delete('/', ['as' => 'category.delete', 'uses' => 'CategoryController@destroy']);

    // Threads
    $r->get('{thread}-{thread_slug}', ['as' => 'thread.show', 'uses' => 'ThreadController@show']);
    $r->get('thread/create', ['as' => 'thread.create', 'uses' => 'ThreadController@create']);
    $r->post('thread/create', ['as' => 'thread.store', 'uses' => 'ThreadController@store']);
    $r->patch('{thread}-{thread_slug}', ['as' => 'thread.update', 'uses' => 'ThreadController@update']);
    $r->delete('{thread}-{thread_slug}', ['as' => 'thread.delete', 'uses' => 'ThreadController@destroy']);

    // Posts
    $r->get('{thread}-{thread_slug}/post/{post}', ['as' => 'post.show', 'uses' => 'PostController@show']);
    $r->get('{thread}-{thread_slug}/reply', ['as' => 'post.create', 'uses' => 'PostController@create']);
    $r->post('{thread}-{thread_slug}/reply', ['as' => 'post.store', 'uses' => 'PostController@store']);
    $r->get('{thread}-{thread_slug}/post/{post}/edit', ['as' => 'post.edit', 'uses' => 'PostController@edit']);
    $r->patch('{thread}-{thread_slug}/{post}', ['as' => 'post.update', 'uses' => 'PostController@update']);
    $r->delete('{thread}-{thread_slug}/{post}', ['as' => 'post.delete', 'uses' => 'PostController@destroy']);
});

// Bulk actions
$r->group(['prefix' => 'bulk', 'as' => 'bulk.'], function ($r) {
    $r->patch('thread', ['as' => 'thread.update', 'uses' => 'ThreadController@bulkUpdate']);
    $r->delete('thread', ['as' => 'thread.delete', 'uses' => 'ThreadController@bulkDestroy']);
    $r->patch('post', ['as' => 'post.update', 'uses' => 'PostController@bulkUpdate']);
    $r->delete('post', ['as' => 'post.delete', 'uses' => 'PostController@bulkDestroy']);
});