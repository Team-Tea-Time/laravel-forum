<?php

$r->group(
    [
        'prefix' => config('forum.routing.api_prefix', "api"),
        'namespace' => 'API',
        'as' => 'api.',
        'middleware' => config('forum.routing.middleware', 'forum.api.auth')
    ], function ($r)
{
    // Categories
    $r->group(['prefix' => 'category', 'as' => 'category.'], function ($r)
    {
        $r->get('/', ['as' => 'index', 'uses' => 'CategoryController@index']);
        $r->post('/', ['as' => 'store', 'uses' => 'CategoryController@store']);
        $r->get('{id}', ['as' => 'fetch', 'uses' => 'CategoryController@fetch']);
        $r->delete('{id}', ['as' => 'delete', 'uses' => 'CategoryController@destroy']);
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
        $r->get('new', ['as' => 'index-new', 'uses' => 'ThreadController@indexNew']);
        $r->patch('new', ['as' => 'mark-new', 'uses' => 'ThreadController@markNew']);
        $r->post('/', ['as' => 'store', 'uses' => 'ThreadController@store']);
        $r->get('{id}', ['as' => 'fetch', 'uses' => 'ThreadController@fetch']);
        $r->delete('{id}', ['as' => 'delete', 'uses' => 'ThreadController@destroy']);
        $r->patch('{id}/restore', ['as' => 'restore', 'uses' => 'ThreadController@restore']);
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
        $r->patch('{id}', ['as' => 'update', 'uses' => 'PostController@update']);
    });

    // Bulk actions
    $r->group(['prefix' => 'bulk', 'as' => 'bulk.'], function ($r)
    {
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
