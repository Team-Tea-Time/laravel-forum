<?php

// Forum index
$r->get('/', ['as' => 'index', 'uses' => "{$controllers['category']}@index"]);

// New/updated threads
$r->get('new', ['as' => 'new.index', 'uses' => "{$controllers['thread']}@indexNew"]);
$r->patch('new/read', ['as' => 'new.mark-read', 'uses' => "{$controllers['thread']}@markRead"]);

// Categories
$r->group(['prefix' => '{category}-{category_slug}'], function ($r) use ($controllers)
{
    $r->get('/', ['as' => 'category.index', 'uses' => "{$controllers['category']}@show"]);

    // Threads
    $r->get('{thread}-{thread_slug}', ['as' => 'thread.show', 'uses' => "{$controllers['thread']}@show"]);
    $r->get('thread/create', ['as' => 'thread.create', 'uses' => "{$controllers['thread']}@create"]);
    $r->post('thread/create', ['as' => 'thread.store', 'uses' => "{$controllers['thread']}@store"]);
    $r->patch('thread/{thread}', ['as' => 'thread.update', 'uses' => "{$controllers['thread']}@update"]);

    // Posts
    $r->get('{thread}-{thread_slug}/post/{post}', ['as' => 'post.show', 'uses' => "{$controllers['post']}@show"]);
    $r->get('{thread}-{thread_slug}/reply', ['as' => 'post.create', 'uses' => "{$controllers['post']}@create"]);
    $r->post('{thread}-{thread_slug}/reply', ['as' => 'post.store', 'uses' => "{$controllers['post']}@store"]);
    $r->get('{thread}-{thread_slug}/post/{post}/edit', ['as' => 'post.edit', 'uses' => "{$controllers['post']}@edit"]);
    $r->patch('{thread}-{thread_slug}/post/{post}/edit', ['as' => 'post.update', 'uses' => "{$controllers['post']}@update"]);
});

// Bulk actions
$r->group(['prefix' => 'bulk', 'as' => 'bulk.'], function ($r) use ($controllers)
{
    $r->patch('thread', ['as' => 'thread.update', 'uses' => "{$controllers['thread']}@bulkUpdate"]);
});

// API
$r->group(['prefix' => 'api', 'namespace' => 'API', 'as' => 'api.', 'middleware' => 'forum.api.auth'], function ($r)
{
    // Categories
    $r->group(['prefix' => 'category', 'as' => 'category.'], function ($r)
    {
        $r->get('/', ['as' => 'index', 'uses' => 'CategoryController@index']);
        $r->post('/', ['as' => 'store', 'uses' => 'CategoryController@store']);
        $r->get('{category}', ['as' => 'fetch', 'uses' => 'CategoryController@fetch']);
        $r->delete('{category}', ['as' => 'delete', 'uses' => 'CategoryController@destroy']);
        $r->patch('{category}/restore', ['as' => 'restore', 'uses' => 'CategoryController@restore']);
        $r->match(['post', 'patch'], '{category}', ['as' => 'update', 'uses' => 'CategoryController@update']);
    });

    // Threads
    $r->group(['prefix' => 'thread', 'as' => 'thread.'], function ($r)
    {
        $r->get('/', ['as' => 'index', 'uses' => 'ThreadController@index']);
        $r->post('/', ['as' => 'store', 'uses' => 'ThreadController@store']);
        $r->get('{thread}', ['as' => 'fetch', 'uses' => 'ThreadController@fetch']);
        $r->delete('{thread}', ['as' => 'delete', 'uses' => 'ThreadController@destroy']);
        $r->patch('{thread}/restore', ['as' => 'restore', 'uses' => 'ThreadController@restore']);
        $r->match(['post', 'patch'], '{thread}', ['as' => 'update', 'uses' => 'ThreadController@update']);
        $r->get('new', ['as' => 'index-new', 'uses' => 'ThreadController@indexNew']);
        $r->patch('new/read', ['as' => 'mark-new', 'uses' => 'ThreadController@markNew']);
        $r->patch('move', ['as' => 'move', 'uses' => 'ThreadController@move']);
        $r->patch('lock', ['as' => 'lock', 'uses' => 'ThreadController@lock']);
        $r->patch('unlock', ['as' => 'unlock', 'uses' => 'ThreadController@unlock']);
        $r->patch('pin', ['as' => 'pin', 'uses' => 'ThreadController@pin']);
        $r->patch('unpin', ['as' => 'unpin', 'uses' => 'ThreadController@unpin']);
    });

    // Posts
    $r->group(['prefix' => 'post', 'as' => 'post.'], function ($r)
    {
        $r->get('/', ['as' => 'index', 'uses' => 'PostController@index']);
        $r->post('/', ['as' => 'store', 'uses' => 'PostController@store']);
        $r->get('{post}', ['as' => 'fetch', 'uses' => 'PostController@fetch']);
        $r->delete('{post}', ['as' => 'delete', 'uses' => 'PostController@destroy']);
        $r->patch('{post}/restore', ['as' => 'restore', 'uses' => 'PostController@restore']);
        $r->match(['post', 'patch'], '{post}', ['as' => 'update', 'uses' => 'PostController@update']);
    });

    // Bulk actions
    $r->group(['prefix' => 'bulk', 'as' => 'bulk.'], function ($r)
    {
        // Categories
        $r->group(['prefix' => 'category', 'as' => 'category.'], function ($r)
        {
            $r->patch('move', ['as' => 'move', 'uses' => 'CategoryController@bulkMove']);
            $r->delete('/', ['as' => 'destroy', 'uses' => 'CategoryController@bulkDestroy']);
            $r->patch('restore', ['as' => 'restore', 'uses' => 'CategoryController@bulkRestore']);
        });

        // Threads
        $r->group(['prefix' => 'thread', 'as' => 'thread.'], function ($r)
        {
            $r->patch('move', ['as' => 'move', 'uses' => 'ThreadController@bulkMove']);
            $r->patch('lock', ['as' => 'lock', 'uses' => 'ThreadController@bulkLock']);
            $r->patch('unlock', ['as' => 'unlock', 'uses' => 'ThreadController@bulkUnlock']);
            $r->patch('pin', ['as' => 'pin', 'uses' => 'ThreadController@bulkPin']);
            $r->patch('unpin', ['as' => 'unpin', 'uses' => 'ThreadController@bulkUnpin']);
            $r->delete('/', ['as' => 'destroy', 'uses' => 'ThreadController@bulkDestroy']);
            $r->patch('restore', ['as' => 'restore', 'uses' => 'ThreadController@bulkRestore']);
        });

        // Posts
        $r->group(['prefix' => 'post', 'as' => 'post.'], function ($r)
        {
            $r->delete('/', ['as' => 'destroy', 'uses' => 'PostController@bulkDestroy']);
            $r->patch('restore', ['as' => 'restore', 'uses' => 'PostController@bulkRestore']);
        });
    });
});
