<?php

Route::group(['prefix' => $root], function () use ($parameters, $controllers)
{
    $category = "{{$parameters['category']}}-{category_slug}";
    $thread = "{{$parameters['thread']}}-{thread_slug}";

    // Forum index
    get('/', ['as' => 'forum.index', 'uses' => "{$controllers['category']}@index"]);

    // New/updated threads
    get('new', ['as' => 'forum.new.index', 'uses' => "{$controllers['thread']}@indexNew"]);
    patch('new/read', ['as' => 'forum.new.mark-read', 'uses' => "{$controllers['thread']}@markRead"]);

    // Categories
    Route::group(['prefix' => $category], function () use ($thread, $parameters, $controllers)
    {
        get('/', ['as' => 'forum.category.index', 'uses' => "{$controllers['category']}@show"]);

        // Threads
        get("{$thread}", ['as' => 'forum.thread.show', 'uses' => "{$controllers['thread']}@show"]);
        get("thread/create", ['as' => 'forum.thread.create', 'uses' => "{$controllers['thread']}@create"]);
        post("thread/create", ['as' => 'forum.thread.store', 'uses' => "{$controllers['thread']}@store"]);

        // Posts
        get("{$thread}/post/{{$parameters['post']}}", ['as' => 'forum.post.show', 'uses' => "{$controllers['post']}@show"]);
        get("{$thread}/reply", ['as' => 'forum.post.create', 'uses' => "{$controllers['post']}@create"]);
        post("{$thread}/reply", ['as' => 'forum.post.store', 'uses' => "{$controllers['post']}@store"]);
        get("{$thread}/post/{{$parameters['post']}}/edit", ['as' => 'forum.post.edit', 'uses' => "{$controllers['post']}@edit"]);
        patch("{$thread}/post/{{$parameters['post']}}/edit", ['as' => 'forum.post.update', 'uses' => "{$controllers['post']}@update"]);
    });

    // API
    Route::group(['prefix' => 'api', 'middleware' => 'auth.basic'], function () use ($parameters, $controllers)
    {
        // Categories
        resource($parameters['category'], 'CategoryController', ['except' => ['create', 'edit']]);
        patch("category/{{$parameters['category']}}/restore", ['as' => 'forum.api.category.restore', 'uses' => 'CategoryController@restore']);

        // Threads
        resource($parameters['thread'], 'ThreadController', ['except' => ['create', 'edit']]);
        patch("thread/{{$parameters['thread']}}/restore", ['as' => 'forum.api.thread.restore', 'uses' => 'ThreadController@restore']);

        // Posts
        resource($parameters['post'], 'PostController', ['except' => ['create', 'edit']]);
        patch("post/{{$parameters['post']}}/restore", ['as' => 'forum.api.post.restore', 'uses' => 'PostController@restore']);

        Route::group(['prefix' => 'bulk'], function ()
        {
            // Categories
            patch('category/move', ['as' => 'forum.api.bulk.category.move', 'uses' => 'CategoryController@bulkMove']);
            delete('category', ['as' => 'forum.api.bulk.category.destroy', 'uses' => 'CategoryController@bulkDestroy']);
            patch('category/restore', ['as' => 'forum.api.bulk.category.restore', 'uses' => 'CategoryController@bulkRestore']);

            // Threads
            patch('thread/move', ['as' => 'forum.api.bulk.thread.move', 'uses' => 'ThreadController@bulkMove']);
            patch('thread/lock', ['as' => 'forum.api.bulk.thread.lock', 'uses' => 'ThreadController@bulkLock']);
            patch('thread/unlock', ['as' => 'forum.api.bulk.thread.unlock', 'uses' => 'ThreadController@bulkUnlock']);
            patch('thread/pin', ['as' => 'forum.api.bulk.thread.pin', 'uses' => 'ThreadController@bulkPin']);
            patch('thread/unpin', ['as' => 'forum.api.bulk.thread.unpin', 'uses' => 'ThreadController@bulkUnpin']);
            delete('thread', ['as' => 'forum.api.bulk.thread.destroy', 'uses' => 'ThreadController@bulkDestroy']);
            patch('thread/restore', ['as' => 'forum.api.bulk.thread.restore', 'uses' => 'ThreadController@bulkRestore']);

            // Posts
            delete('post', ['as' => 'forum.api.bulk.post.destroy', 'uses' => 'PostController@bulkDestroy']);
            patch('post/restore', ['as' => 'forum.api.bulk.post.restore', 'uses' => 'PostController@bulkRestore']);
        });
    });
});

// Model binding
Route::bind($parameters['category'], function ($id)
{
    return Forum::bindParameter(new \Riari\Forum\Models\Category, $id);
});
Route::bind($parameters['thread'], function ($id)
{
    return Forum::bindParameter(new \Riari\Forum\Models\Thread, $id);
});
Route::bind($parameters['post'], function ($id)
{
    return Forum::bindParameter(new \Riari\Forum\Models\Post, $id);
});
