<?php

use TeamTeaTime\Forum\Http\Livewire\Pages\{
    CategoryCreate,
    CategoryEdit,
    CategoryIndex,
    CategoryShow,
    PostsPendingApproval,
    PostEdit,
    PostShow,
    RecentThreads,
    ThreadsPendingApproval,
    ThreadCreate,
    ThreadReply,
    ThreadShow,
    UnreadThreads,
    UpdateCategoryTree,
};

$prefix = config('forum.frontend.route_prefixes');

Route::livewire('/', 'forum.pages.category.index')->name('category.index');
Route::livewire('category/order', 'forum.pages.category.manage')->name('category.order');
Route::livewire('category/create', 'forum.pages.category.create')->name('category.create');

Route::livewire('recent', 'forum.pages.thread.recent')->name('recent');
Route::livewire('unread', 'forum.pages.thread.unread')->name('unread');
Route::livewire('pending-approval/threads', 'forum.pages.thread.pending-approval')->name('pending-approval.threads');
Route::livewire('pending-approval/posts', 'forum.pages.post.pending-approval')->name('pending-approval.posts');

Route::group(['prefix' => $prefix['category'] . '/{category_id}-{category_slug}'], function () use ($prefix) {
    Route::livewire('/', 'forum.pages.category.show')->name('category.show');
    Route::livewire('edit', 'forum.pages.category.edit')->name('category.edit');
    Route::livewire($prefix['thread'] . '/create', 'forum.pages.thread.create')->name('thread.create');
});

Route::group(['prefix' => $prefix['thread'] . '/{thread_id}-{thread_slug}'], function () use ($prefix) {
    Route::livewire('/', 'forum.pages.thread.show')->name('thread.show');
    Route::livewire('reply', 'forum.pages.thread.reply')->name('thread.reply');
    Route::livewire($prefix['post'] . '/{post_id}/edit', 'forum.pages.post.edit')->name('post.edit');
    Route::livewire($prefix['post'] . '/{post_id}', 'forum.pages.post.show')->name('post.show');
});
