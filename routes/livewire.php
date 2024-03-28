<?php

use TeamTeaTime\Forum\Http\Livewire\Pages\{
    CategoryCreate,
    CategoryIndex,
    CategoryShow,
    ManageCategories,
    ThreadCreate,
    ThreadShow,
};

$prefix = config('forum.frontend.route_prefixes');

Route::get('/', CategoryIndex::class)->name('category.index');
Route::get('manage', ManageCategories::class)->name('category.manage');
Route::get('category/create', CategoryCreate::class)->name('category.create');

Route::group(['prefix' => $prefix['category'] . '/{category_id}-{category_slug}'], function () use ($prefix)
{
    Route::get('/', CategoryShow::class)->name('category.show');
    Route::get($prefix['thread'] . '/create', ThreadCreate::class)->name('thread.create');
});

Route::group(['prefix' => $prefix['thread'] . '/{thread_id}-{thread_slug}'], function () use ($prefix)
{
    Route::get('/', ThreadShow::class)->name('thread.show');
});
