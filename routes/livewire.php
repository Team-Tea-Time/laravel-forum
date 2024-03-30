<?php

use TeamTeaTime\Forum\Http\Livewire\Pages\{
    CategoryCreate,
    CategoryEdit,
    CategoryIndex,
    CategoryShow,
    PostEdit,
    PostShow,
    RecentThreads,
    ThreadCreate,
    ThreadReply,
    ThreadShow,
    UnreadThreads,
    UpdateCategoryTree,
};

$prefix = config('forum.frontend.route_prefixes');

Route::get('/', CategoryIndex::class)->name('category.index');
Route::get('category/order', UpdateCategoryTree::class)->name('category.order');
Route::get('category/create', CategoryCreate::class)->name('category.create');

Route::get('recent', RecentThreads::class)->name('recent');
Route::get('unread', UnreadThreads::class)->name('unread');

Route::group(['prefix' => $prefix['category'] . '/{category_id}-{category_slug}'], function () use ($prefix) {
    Route::get('/', CategoryShow::class)->name('category.show');
    Route::get('edit', CategoryEdit::class)->name('category.edit');
    Route::get($prefix['thread'] . '/create', ThreadCreate::class)->name('thread.create');
});

Route::group(['prefix' => $prefix['thread'] . '/{thread_id}-{thread_slug}'], function () use ($prefix) {
    Route::get('/', ThreadShow::class)->name('thread.show');
    Route::get('reply', ThreadReply::class)->name('thread.reply');
    Route::get($prefix['post'] . '/{post_id}/edit', PostEdit::class)->name('post.edit');
    Route::get($prefix['post'] . '/{post_id}', PostShow::class)->name('post.show');
});
