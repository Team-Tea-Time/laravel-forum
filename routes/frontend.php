<?php

$prefix = config('forum.frontend.route_prefixes');

$r->get('/', ['as' => 'index', 'uses' => 'CategoryController@index']);

$r->get('recent', ['as' => 'recent', 'uses' => 'ThreadController@recent']);

$r->get('unread', ['as' => 'unread', 'uses' => 'ThreadController@unread'])->middleware('auth');
$r->patch('unread', ['as' => 'mark-read', 'uses' => 'ThreadController@markRead'])->middleware('auth');

$r->get('manage', ['as' => 'category.manage', 'uses' => 'CategoryController@manage']);

$r->post($prefix['category'] . '/create', ['as' => 'category.store', 'uses' => 'CategoryController@store']);
$r->group(['prefix' => $prefix['category'] . '/{category}-{category_slug}'], function ($r) use ($prefix)
{
    $r->get('/', ['as' => 'category.show', 'uses' => 'CategoryController@show']);
    $r->patch('/', ['as' => 'category.update', 'uses' => 'CategoryController@update']);
    $r->delete('/', ['as' => 'category.delete', 'uses' => 'CategoryController@destroy']);

    $r->get($prefix['thread'] . '/create', ['as' => 'thread.create', 'uses' => 'ThreadController@create']);
    $r->post($prefix['thread'] . '/create', ['as' => 'thread.store', 'uses' => 'ThreadController@store']);
});

$r->group(['prefix' => $prefix['thread'] . '/{thread}-{thread_slug}'], function ($r) use ($prefix)
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
    
    $r->get($prefix['post'] . '/{post}', ['as' => 'post.show', 'uses' => 'PostController@show']);
    $r->get('reply', ['as' => 'post.create', 'uses' => 'PostController@create']);
    $r->post('reply', ['as' => 'post.store', 'uses' => 'PostController@store']);
    $r->get($prefix['post'] . '/{post}/edit', ['as' => 'post.edit', 'uses' => 'PostController@edit']);
    $r->patch($prefix['post'] . '/{post}', ['as' => 'post.update', 'uses' => 'PostController@update']);
    $r->get($prefix['post'] . '/{post}/delete', ['as' => 'post.confirm-delete', 'uses' => 'PostController@confirmDelete']);
    $r->get($prefix['post'] . '/{post}/restore', ['as' => 'post.confirm-restore', 'uses' => 'PostController@confirmRestore']);
    $r->delete($prefix['post'] . '/{post}', ['as' => 'post.destroy', 'uses' => 'PostController@destroy']);
    $r->post($prefix['post'] . '/{post}/restore', ['as' => 'post.restore', 'uses' => 'PostController@restore']);
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

$r->bind('post', function ($value)
{
    $post = \TeamTeaTime\Forum\Models\Post::withTrashed()->find($value);

    if ($post->trashed() && ! Gate::allows('viewTrashedPosts')) return null;

    return $post;
});