<?php

// Forum index
get($root, ['as' => 'forum.index', 'uses' => "{$controllers['category']}@index"]);

Route::group(['prefix' => $root], function () use ($parameters, $controllers)
{
    $category = "{{$parameters['category']}}-{category_slug}";
    $thread = "{{$parameters['thread']}}-{thread_slug}";

    // New
    get('new', ['as' => 'forum.new.index', 'uses' => "{$controllers['thread']}@indexNew"]);
    patch('new/read', ['as' => 'forum.new.mark-read', 'uses' => "{$controllers['thread']}@markRead"]);

    // Categories
    get($category, ['as' => 'forum.category.index', 'uses' => "{$controllers['category']}@show"]);

    // Threads
    get("{$category}/{$thread}", ['as' => 'forum.thread.show', 'uses' => "{$controllers['thread']}@show"]);
    get("{$category}/thread/create", ['as' => 'forum.thread.create', 'uses' => "{$controllers['thread']}@create"]);
    post("{$category}/thread/create", ['as' => 'forum.thread.store', 'uses' => "{$controllers['thread']}@store"]);

    // Posts
    get("{$category}/{$thread}/post/{{$parameters['post']}}", ['as' => 'forum.post.show', 'uses' => "{$controllers['post']}@show"]);
    get("{$category}/{$thread}/reply", ['as' => 'forum.post.create', 'uses' => "{$controllers['post']}@create"]);
    post("{$category}/{$thread}/reply", ['as' => 'forum.post.store', 'uses' => "{$controllers['post']}@store"]);
    get("{$category}/{$thread}/post/{{$parameters['post']}}/edit", ['as' => 'forum.post.edit', 'uses' => "{$controllers['post']}@edit"]);
    patch("{$category}/{$thread}/post/{{$parameters['post']}}/edit", ['as' => 'forum.post.update', 'uses' => "{$controllers['post']}@update"]);
});

// Model binding
Route::bind($parameters['category'], function ($id)
{
    return Forum::bindParameter(new \Riari\Forum\Models\Category, $id);
});
Route::bind($parameters['thread'], function ($id)
{
    $model = new \Riari\Forum\Models\Thread;
    return Forum::bindParameter($model->withTrashed(), $id);
});
Route::bind($parameters['post'], function ($id)
{
    $model = new \Riari\Forum\Models\Post;
    return Forum::bindParameter($model->withTrashed(), $id);
});
