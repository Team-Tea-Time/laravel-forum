<?php

// Forum index
get('/', ['as' => 'index', 'uses' => "{$controllers['category']}@index"]);

// New/updated threads
get('new', ['as' => 'new.index', 'uses' => "{$controllers['thread']}@indexNew"]);
patch('new/read', ['as' => 'new.mark-read', 'uses' => "{$controllers['thread']}@markRead"]);

// Categories
Route::group(['prefix' => '{category}-{category_slug}'], function () use ($controllers)
{
    get('/', ['as' => 'category.index', 'uses' => "{$controllers['category']}@show"]);

    // Threads
    get("{thread}-{thread_slug}", ['as' => 'thread.show', 'uses' => "{$controllers['thread']}@show"]);
    get("thread/create", ['as' => 'thread.create', 'uses' => "{$controllers['thread']}@create"]);
    post("thread/create", ['as' => 'thread.store', 'uses' => "{$controllers['thread']}@store"]);

    // Posts
    get("{thread}-{thread_slug}/post/{post}", ['as' => 'post.show', 'uses' => "{$controllers['post']}@show"]);
    get("{thread}-{thread_slug}/reply", ['as' => 'post.create', 'uses' => "{$controllers['post']}@create"]);
    post("{thread}-{thread_slug}/reply", ['as' => 'post.store', 'uses' => "{$controllers['post']}@store"]);
    get("{thread}-{thread_slug}/post/{post}/edit", ['as' => 'post.edit', 'uses' => "{$controllers['post']}@edit"]);
    patch("{thread}-{thread_slug}/post/{post}/edit", ['as' => 'post.update', 'uses' => "{$controllers['post']}@update"]);
});

// API
Route::group(['prefix' => 'api', 'namespace' => 'API', 'middleware' => 'auth.basic'], function () use ($controllers)
{
    // Categories
    resource('category', 'CategoryController', ['except' => ['create', 'edit']]);
    patch("category/{category}/restore", ['as' => 'api.category.restore', 'uses' => 'CategoryController@restore']);

    // Threads
    resource('thread', 'ThreadController', ['except' => ['create', 'edit']]);
    get('thread/new', ['as' => 'api.thread.new.index', 'uses' => 'ThreadController@indexNew']);
    patch('thread/new/read', ['as' => 'api.thread.new.mark', 'uses' => 'ThreadController@markNew']);
    patch("thread/{thread}/restore", ['as' => 'api.thread.restore', 'uses' => 'ThreadController@restore']);

    // Posts
    resource('post', 'PostController', ['except' => ['create', 'edit']]);
    patch("post/{post}/restore", ['as' => 'api.post.restore', 'uses' => 'PostController@restore']);

    Route::group(['prefix' => 'bulk'], function ()
    {
        // Categories
        patch('category/move', ['as' => 'api.bulk.category.move', 'uses' => 'CategoryController@bulkMove']);
        delete('category', ['as' => 'api.bulk.category.destroy', 'uses' => 'CategoryController@bulkDestroy']);
        patch('category/restore', ['as' => 'api.bulk.category.restore', 'uses' => 'CategoryController@bulkRestore']);

        // Threads
        patch('thread/move', ['as' => 'api.bulk.thread.move', 'uses' => 'ThreadController@bulkMove']);
        patch('thread/lock', ['as' => 'api.bulk.thread.lock', 'uses' => 'ThreadController@bulkLock']);
        patch('thread/unlock', ['as' => 'api.bulk.thread.unlock', 'uses' => 'ThreadController@bulkUnlock']);
        patch('thread/pin', ['as' => 'api.bulk.thread.pin', 'uses' => 'ThreadController@bulkPin']);
        patch('thread/unpin', ['as' => 'api.bulk.thread.unpin', 'uses' => 'ThreadController@bulkUnpin']);
        delete('thread', ['as' => 'api.bulk.thread.destroy', 'uses' => 'ThreadController@bulkDestroy']);
        patch('thread/restore', ['as' => 'api.bulk.thread.restore', 'uses' => 'ThreadController@bulkRestore']);

        // Posts
        delete('post', ['as' => 'api.bulk.post.destroy', 'uses' => 'PostController@bulkDestroy']);
        patch('post/restore', ['as' => 'api.bulk.post.restore', 'uses' => 'PostController@bulkRestore']);
    });
});
