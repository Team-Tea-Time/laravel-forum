<?php

$authMiddleware = config('forum.web.router.auth_middleware');
$prefix = config('forum.web.route_prefixes');

// Standalone routes
$r->get('/', ['as' => 'index', 'uses' => 'CategoryController@index']);

$r->get('recent', ['as' => 'recent', 'uses' => 'ThreadController@recent']);

$r->get('unread', ['as' => 'unread', 'uses' => 'ThreadController@unread']);
$r->patch('unread/mark-as-read', ['as' => 'unread.mark-as-read', 'uses' => 'ThreadController@markAsRead'])->middleware($authMiddleware);

$r->get('manage', ['as' => 'category.manage', 'uses' => 'CategoryController@manage'])->middleware($authMiddleware);

// Categories
$r->post($prefix['category'].'/create', ['as' => 'category.store', 'uses' => 'CategoryController@store']);
$r->group(['prefix' => $prefix['category'].'/{category}-{category_slug}'], function ($r) use ($prefix, $authMiddleware) {
    $r->get('/', ['as' => 'category.show', 'uses' => 'CategoryController@show']);
    $r->patch('/', ['as' => 'category.update', 'uses' => 'CategoryController@update'])->middleware($authMiddleware);
    $r->delete('/', ['as' => 'category.delete', 'uses' => 'CategoryController@delete'])->middleware($authMiddleware);

    $r->get($prefix['thread'].'/create', ['as' => 'thread.create', 'uses' => 'ThreadController@create']);
    $r->post($prefix['thread'].'/create', ['as' => 'thread.store', 'uses' => 'ThreadController@store'])->middleware($authMiddleware);
});

// Threads
$r->group(['prefix' => $prefix['thread'].'/{thread}-{thread_slug}'], function ($r) use ($prefix, $authMiddleware) {
    $r->get('/', ['as' => 'thread.show', 'uses' => 'ThreadController@show']);
    $r->get($prefix['post'].'/{post}', ['as' => 'post.show', 'uses' => 'PostController@show']);

    $r->group(['middleware' => $authMiddleware], function ($r) use ($prefix) {
        $r->patch('/', ['as' => 'thread.update', 'uses' => 'ThreadController@update']);
        $r->post('lock', ['as' => 'thread.lock', 'uses' => 'ThreadController@lock']);
        $r->post('unlock', ['as' => 'thread.unlock', 'uses' => 'ThreadController@unlock']);
        $r->post('pin', ['as' => 'thread.pin', 'uses' => 'ThreadController@pin']);
        $r->post('unpin', ['as' => 'thread.unpin', 'uses' => 'ThreadController@unpin']);
        $r->post('move', ['as' => 'thread.move', 'uses' => 'ThreadController@move']);
        $r->post('restore', ['as' => 'thread.restore', 'uses' => 'ThreadController@restore']);
        $r->post('rename', ['as' => 'thread.rename', 'uses' => 'ThreadController@rename']);
        $r->delete('/', ['as' => 'thread.delete', 'uses' => 'ThreadController@delete']);

        $r->get('reply', ['as' => 'post.create', 'uses' => 'PostController@create']);
        $r->post('reply', ['as' => 'post.store', 'uses' => 'PostController@store']);
        $r->get($prefix['post'].'/{post}/edit', ['as' => 'post.edit', 'uses' => 'PostController@edit']);
        $r->patch($prefix['post'].'/{post}', ['as' => 'post.update', 'uses' => 'PostController@update']);
        $r->get($prefix['post'].'/{post}/delete', ['as' => 'post.confirm-delete', 'uses' => 'PostController@confirmDelete']);
        $r->get($prefix['post'].'/{post}/restore', ['as' => 'post.confirm-restore', 'uses' => 'PostController@confirmRestore']);
        $r->delete($prefix['post'].'/{post}', ['as' => 'post.delete', 'uses' => 'PostController@delete']);
        $r->post($prefix['post'].'/{post}/restore', ['as' => 'post.restore', 'uses' => 'PostController@restore']);
    });
});

// Bulk actions
$r->group(['prefix' => 'bulk', 'as' => 'bulk.', 'namespace' => 'Bulk', 'middleware' => $authMiddleware], function ($r) {
    // Categories
    $r->post('category/manage', ['as' => 'category.manage', 'uses' => 'CategoryController@manage']);

    // Threads
    $r->group(['prefix' => 'thread', 'as' => 'thread.'], function ($r) {
        $r->post('move', ['as' => 'move', 'uses' => 'ThreadController@move']);
        $r->post('lock', ['as' => 'lock', 'uses' => 'ThreadController@lock']);
        $r->post('unlock', ['as' => 'unlock', 'uses' => 'ThreadController@unlock']);
        $r->post('pin', ['as' => 'pin', 'uses' => 'ThreadController@pin']);
        $r->post('unpin', ['as' => 'unpin', 'uses' => 'ThreadController@unpin']);
        $r->delete('/', ['as' => 'delete', 'uses' => 'ThreadController@delete']);
        $r->post('restore', ['as' => 'restore', 'uses' => 'ThreadController@restore']);
    });

    // Posts
    $r->group(['prefix' => 'post', 'as' => 'post.'], function ($r) {
        $r->delete('/', ['as' => 'delete', 'uses' => 'PostController@delete']);
        $r->post('restore', ['as' => 'restore', 'uses' => 'PostController@restore']);
    });
});

$r->bind('category', function ($value) {
    return \TeamTeaTime\Forum\Models\Category::findOrFail($value);
});

$r->bind('thread', function ($value) {
    $query = \TeamTeaTime\Forum\Models\Thread::with('category');

    if (Gate::allows('viewTrashedThreads')) {
        $query->withTrashed();
    }

    $thread = $query->find($value);

    if ($thread === null) {
        abort(404);
    }

    return $thread;
});

$r->bind('post', function ($value) {
    $query = \TeamTeaTime\Forum\Models\Post::with(['thread', 'thread.category']);

    if (Gate::allows('viewTrashedPosts')) {
        $query->withTrashed();
    }

    $post = $query->find($value);

    if ($post === null) {
        abort(404);
    }

    return $post;
});
