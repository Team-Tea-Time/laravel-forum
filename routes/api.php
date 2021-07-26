<?php

// Categories
$r->group(['prefix' => 'category', 'as' => 'category.'], function ($r)
{
    $r->get('/', ['as' => 'index', 'uses' => 'CategoryController@index']);
    $r->get('{category}', ['as' => 'fetch', 'uses' => 'CategoryController@fetch']);
    $r->post('/', ['as' => 'store', 'uses' => 'CategoryController@store']);
    $r->patch('{category}', ['as' => 'update', 'uses' => 'CategoryController@update']);
    $r->delete('{category}', ['as' => 'delete', 'uses' => 'CategoryController@destroy']);

    // Threads by category
    $r->get('{category}/thread', ['as' => 'threads', 'uses' => 'ThreadController@indexByCategory']);
    $r->post('{category}/thread', ['as' => 'threads.store', 'uses' => 'ThreadController@store']);
});

// Threads
$r->group(['prefix' => 'thread', 'as' => 'thread.'], function ($r)
{
    $r->get('recent', ['as' => 'recent', 'uses' => 'ThreadController@recent']);
    $r->get('unread', ['as' => 'unread', 'uses' => 'ThreadController@unread']);
    $r->patch('unread/mark-as-read', ['as' => 'unread.mark-as-read', 'uses' => 'ThreadController@markAsRead']);
    $r->get('{thread}', ['as' => 'fetch', 'uses' => 'ThreadController@fetch']);
    $r->post('{thread}/lock', ['as' => 'lock', 'uses' => 'ThreadController@lock']);
    $r->post('{thread}/unlock', ['as' => 'unlock', 'uses' => 'ThreadController@unlock']);
    $r->post('{thread}/pin', ['as' => 'pin', 'uses' => 'ThreadController@pin']);
    $r->post('{thread}/unpin', ['as' => 'unpin', 'uses' => 'ThreadController@unpin']);
    $r->post('{thread}/rename', ['as' => 'rename', 'uses' => 'ThreadController@rename']);
    $r->post('{thread}/move', ['as' => 'move', 'uses' => 'ThreadController@move']);
    $r->delete('{thread}', ['as' => 'delete', 'uses' => 'ThreadController@delete']);
    $r->post('{thread}/restore', ['as' => 'restore', 'uses' => 'ThreadController@restore']);

    // Posts by thread
    $r->get('{thread}/posts', ['as' => 'posts', 'uses' => 'PostController@indexByThread']);
});

// Posts
$r->group(['prefix' => 'post', 'as' => 'post.'], function ($r)
{
    $r->post('search', ['as' => 'search', 'uses' => 'PostController@search']);
    $r->get('recent', ['as' => 'recent', 'uses' => 'PostController@recent']);
    $r->get('unread', ['as' => 'unread', 'uses' => 'PostController@unread']);
});

// Bulk actions
$r->group(['prefix' => 'bulk', 'as' => 'bulk.', 'namespace' => 'Bulk'], function ($r)
{
    // Categories
    $r->group(['prefix' => 'category', 'as' => 'category.'], function ($r)
    {
        $r->post('manage', ['as' => 'manage', 'uses' => 'CategoryController@manage']);
    });

    // Threads
    $r->group(['prefix' => 'thread', 'as' => 'thread.'], function ($r)
    {
        $r->post('move', ['as' => 'move', 'uses' => 'ThreadController@move']);
        $r->post('lock', ['as' => 'lock', 'uses' => 'ThreadController@lock']);
        $r->post('unlock', ['as' => 'unlock', 'uses' => 'ThreadController@unlock']);
        $r->post('pin', ['as' => 'pin', 'uses' => 'ThreadController@pin']);
        $r->post('unpin', ['as' => 'unpin', 'uses' => 'ThreadController@unpin']);
        $r->delete('/', ['as' => 'delete', 'uses' => 'ThreadController@destroy']);
        $r->post('restore', ['as' => 'restore', 'uses' => 'ThreadController@restore']);
    });
});

$r->bind('category', function ($value)
{
    return \TeamTeaTime\Forum\Models\Category::find($value);
});

$r->bind('thread', function ($value)
{
    $thread = \TeamTeaTime\Forum\Models\Thread::withTrashed()->with('category')->find($value);

    if ($thread->trashed() && ! Gate::allows('viewTrashedThreads')) return null;

    return $thread;
});

$r->bind('post', function ($value)
{
    $post = \TeamTeaTime\Forum\Models\Post::withTrashed()->with(['thread', 'thread.category'])->find($value);

    if ($post->trashed() && ! Gate::allows('viewTrashedPosts')) return null;

    return $post;
});
