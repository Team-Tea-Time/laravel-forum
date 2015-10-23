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

    // Posts
    $r->get('{thread}-{thread_slug}/post/{post}', ['as' => 'post.show', 'uses' => "{$controllers['post']}@show"]);
    $r->get('{thread}-{thread_slug}/reply', ['as' => 'post.create', 'uses' => "{$controllers['post']}@create"]);
    $r->get('{thread}-{thread_slug}/post/{post}/edit', ['as' => 'post.edit', 'uses' => "{$controllers['post']}@edit"]);
});

// Actions
$r->post('category/create', ['as' => 'category.store', 'uses' => "{$controllers['category']}@store"]);
$r->patch('category/{category}', ['as' => 'category.update', 'uses' => "{$controllers['category']}@update"]);
$r->delete('category/{category}', ['as' => 'category.delete', 'uses' => "{$controllers['category']}@destroy"]);
$r->post('category/{category}/thread/create', ['as' => 'thread.store', 'uses' => "{$controllers['thread']}@store"]);
$r->patch('thread/{thread}', ['as' => 'thread.update', 'uses' => "{$controllers['thread']}@update"]);
$r->delete('thread/{thread}', ['as' => 'thread.delete', 'uses' => "{$controllers['thread']}@destroy"]);
$r->post('thread/{thread}/reply', ['as' => 'post.store', 'uses' => "{$controllers['post']}@store"]);
$r->patch('post/{post}', ['as' => 'post.update', 'uses' => "{$controllers['post']}@update"]);
$r->delete('post/{post}', ['as' => 'post.delete', 'uses' => "{$controllers['post']}@destroy"]);

// Bulk actions
$r->group(['prefix' => 'bulk', 'as' => 'bulk.'], function ($r) use ($controllers)
{
    $r->patch('thread', ['as' => 'thread.update', 'uses' => "{$controllers['thread']}@bulkUpdate"]);
    $r->delete('thread', ['as' => 'thread.delete', 'uses' => "{$controllers['thread']}@bulkDestroy"]);
    $r->patch('post', ['as' => 'post.update', 'uses' => "{$controllers['post']}@bulkUpdate"]);
    $r->delete('post', ['as' => 'post.delete', 'uses' => "{$controllers['post']}@bulkDestroy"]);
});

// API
$r->group(['prefix' => 'api', 'namespace' => 'API', 'as' => 'api.', 'middleware' => 'forum.api.auth'], function ($r)
{
    // Categories
    $r->group(['prefix' => 'category', 'as' => 'category.'], function ($r)
    {
        $r->get('/', ['as' => 'index', 'uses' => 'CategoryController@index']);
        $r->post('/', ['as' => 'store', 'uses' => 'CategoryController@store']);
        $r->get('{id}', ['as' => 'fetch', 'uses' => 'CategoryController@fetch']);
        $r->delete('{id}', ['as' => 'delete', 'uses' => 'CategoryController@destroy']);
        $r->patch('{id}/restore', ['as' => 'restore', 'uses' => 'CategoryController@restore']);
        $r->patch('{id}/enable-threads', ['as' => 'enable-threads', 'uses' => 'CategoryController@enableThreads']);
        $r->patch('{id}/disable-threads', ['as' => 'disable-threads', 'uses' => 'CategoryController@disableThreads']);
        $r->patch('{id}/make-public', ['as' => 'make-public', 'uses' => 'CategoryController@makePublic']);
        $r->patch('{id}/make-private', ['as' => 'make-private', 'uses' => 'CategoryController@makePrivate']);
        $r->patch('{id}/move', ['as' => 'move', 'uses' => 'CategoryController@move']);
        $r->patch('{id}/rename', ['as' => 'rename', 'uses' => 'CategoryController@rename']);
        $r->patch('{id}/reorder', ['as' => 'reorder', 'uses' => 'CategoryController@reorder']);
    });

    // Threads
    $r->group(['prefix' => 'thread', 'as' => 'thread.'], function ($r)
    {
        $r->get('/', ['as' => 'index', 'uses' => 'ThreadController@index']);
        $r->post('/', ['as' => 'store', 'uses' => 'ThreadController@store']);
        $r->get('{id}', ['as' => 'fetch', 'uses' => 'ThreadController@fetch']);
        $r->delete('{id}', ['as' => 'delete', 'uses' => 'ThreadController@destroy']);
        $r->patch('{id}/restore', ['as' => 'restore', 'uses' => 'ThreadController@restore']);
        $r->get('new', ['as' => 'index-new', 'uses' => 'ThreadController@indexNew']);
        $r->patch('new/read', ['as' => 'mark-new', 'uses' => 'ThreadController@markNew']);
        $r->patch('{id}/move', ['as' => 'move', 'uses' => 'ThreadController@move']);
        $r->patch('{id}/lock', ['as' => 'lock', 'uses' => 'ThreadController@lock']);
        $r->patch('{id}/unlock', ['as' => 'unlock', 'uses' => 'ThreadController@unlock']);
        $r->patch('{id}/pin', ['as' => 'pin', 'uses' => 'ThreadController@pin']);
        $r->patch('{id}/unpin', ['as' => 'unpin', 'uses' => 'ThreadController@unpin']);
        $r->patch('{id}/rename', ['as' => 'rename', 'uses' => 'ThreadController@rename']);
    });

    // Posts
    $r->group(['prefix' => 'post', 'as' => 'post.'], function ($r)
    {
        $r->get('/', ['as' => 'index', 'uses' => 'PostController@index']);
        $r->post('/', ['as' => 'store', 'uses' => 'PostController@store']);
        $r->get('{id}', ['as' => 'fetch', 'uses' => 'PostController@fetch']);
        $r->delete('{id}', ['as' => 'delete', 'uses' => 'PostController@destroy']);
        $r->patch('{id}/restore', ['as' => 'restore', 'uses' => 'PostController@restore']);
        $r->match(['post', 'patch'], '{post}', ['as' => 'update', 'uses' => 'PostController@update']);
    });

    // Bulk actions
    $r->group(['prefix' => 'bulk', 'as' => 'bulk.'], function ($r)
    {
        // Categories
        $r->group(['prefix' => 'category', 'as' => 'category.'], function ($r)
        {
            $r->patch('move', ['as' => 'move', 'uses' => 'CategoryController@bulkMove']);
            $r->delete('/', ['as' => 'delete', 'uses' => 'CategoryController@bulkDestroy']);
            $r->patch('restore', ['as' => 'restore', 'uses' => 'CategoryController@bulkRestore']);
        });

        // Threads
        $r->group(['prefix' => 'thread', 'as' => 'thread.'], function ($r)
        {
            $r->delete('/', ['as' => 'delete', 'uses' => 'ThreadController@bulkDestroy']);
            $r->patch('restore', ['as' => 'restore', 'uses' => 'ThreadController@bulkRestore']);
            $r->patch('move', ['as' => 'move', 'uses' => 'ThreadController@bulkMove']);
            $r->patch('lock', ['as' => 'lock', 'uses' => 'ThreadController@bulkLock']);
            $r->patch('unlock', ['as' => 'unlock', 'uses' => 'ThreadController@bulkUnlock']);
            $r->patch('pin', ['as' => 'pin', 'uses' => 'ThreadController@bulkPin']);
            $r->patch('unpin', ['as' => 'unpin', 'uses' => 'ThreadController@bulkUnpin']);
        });

        // Posts
        $r->group(['prefix' => 'post', 'as' => 'post.'], function ($r)
        {
            $r->patch('/', ['as' => 'update', 'uses' => 'PostController@bulkUpdate']);
            $r->delete('/', ['as' => 'delete', 'uses' => 'PostController@bulkDestroy']);
            $r->patch('restore', ['as' => 'restore', 'uses' => 'PostController@bulkRestore']);
        });
    });
});
