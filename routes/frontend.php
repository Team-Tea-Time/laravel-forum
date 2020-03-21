<?php

$r->get('/', ['as' => 'index', 'uses' => 'CategoryController@index']);

$r->get('recent', ['as' => 'recent', 'uses' => 'ThreadController@recent']);

$r->get('unread', ['as' => 'unread', 'uses' => 'ThreadController@unread'])->middleware('auth');
$r->patch('unread', ['as' => 'mark-read', 'uses' => 'ThreadController@markRead'])->middleware('auth');

$r->get('manage', ['as' => 'category.manage', 'uses' => 'CategoryController@manage']);

$categoryPrefix = config('forum.frontend.router.category_prefix');
$r->post($categoryPrefix . '/create', ['as' => 'category.store', 'uses' => 'CategoryController@store']);
$r->group(['prefix' => $categoryPrefix . '/{category}-{category_slug}'], function ($r)
{
    $r->get('/', ['as' => 'category.show', 'uses' => 'CategoryController@show']);
    $r->patch('/', ['as' => 'category.update', 'uses' => 'CategoryController@update']);
    $r->delete('/', ['as' => 'category.delete', 'uses' => 'CategoryController@destroy']);

    $r->get('t/create', ['as' => 'thread.create', 'uses' => 'ThreadController@create']);
    $r->post('t/create', ['as' => 'thread.store', 'uses' => 'ThreadController@store']);
});

$threadPrefix = config('forum.frontend.router.thread_prefix');
$r->group(['prefix' => $threadPrefix . '/{thread}-{thread_slug}'], function ($r)
{
    $r->get('/', ['as' => 'thread.show', 'uses' => 'ThreadController@show']);
    $r->patch('/', ['as' => 'thread.update', 'uses' => 'ThreadController@update']);
    $r->post('lock', ['as' => 'thread.lock', 'uses' => 'ThreadController@lock']);
    $r->post('unlock', ['as' => 'thread.unlock', 'uses' => 'ThreadController@unlock']);
    $r->post('pin', ['as' => 'thread.pin', 'uses' => 'ThreadController@pin']);
    $r->post('unpin', ['as' => 'thread.unpin', 'uses' => 'ThreadController@unpin']);
    $r->post('move', ['as' => 'thread.move', 'uses' => 'ThreadController@move']);
    $r->post('restore', ['as' => 'thread.restore', 'uses' => 'ThreadController@restore']);
    $r->post('rename', ['as' => 'thread.rename', 'uses' => 'ThreadController@rename']);
    $r->delete('/', ['as' => 'thread.delete', 'uses' => 'ThreadController@destroy']);
    
    $r->get('post/{post}', ['as' => 'post.show', 'uses' => 'PostController@show']);
    $r->get('reply', ['as' => 'post.create', 'uses' => 'PostController@create']);
    $r->post('reply', ['as' => 'post.store', 'uses' => 'PostController@store']);
    $r->get('post/{post}/edit', ['as' => 'post.edit', 'uses' => 'PostController@edit']);
    $r->patch('{post}', ['as' => 'post.update', 'uses' => 'PostController@update']);
    $r->delete('{post}', ['as' => 'post.delete', 'uses' => 'PostController@destroy']);
});

$r->group(['prefix' => 'bulk', 'as' => 'bulk.'], function ($r)
{
    $r->patch('thread', ['as' => 'thread.update', 'uses' => 'ThreadController@bulkUpdate']);
    $r->delete('thread', ['as' => 'thread.delete', 'uses' => 'ThreadController@bulkDestroy']);
    $r->patch('post', ['as' => 'post.update', 'uses' => 'PostController@bulkUpdate']);
    $r->delete('post', ['as' => 'post.delete', 'uses' => 'PostController@bulkDestroy']);
});

$r->bind('thread', function ($value)
{
    $thread = \TeamTeaTime\Forum\Models\Thread::withTrashed()->find($value);

    if ($thread->trashed() && ! Gate::allows('viewTrashedThreads')) return null;

    return $thread;
});