<?php

use TeamTeaTime\Forum\Http\Livewire\Pages\CategoryIndex;
use TeamTeaTime\Forum\Http\Livewire\Pages\CategoryShow;
use TeamTeaTime\Forum\Http\Livewire\Pages\ThreadCreate;

$prefix = config('forum.frontend.route_prefixes');

Route::get('/', CategoryIndex::class)->name('category.index');

Route::group(['prefix' => $prefix['category'] . '/{category_id}-{category_slug}'], function () use ($prefix)
{
    Route::get('/', CategoryShow::class)->name('category.show');
    Route::get($prefix['thread'] . '/create', ThreadCreate::class)->name('thread.create');
});

Route::group(['prefix' => $prefix['thread'] . '/{thread_id}-{thread_slug}'], function () use ($prefix)
{
    Route::get('/', CategoryShow::class)->name('thread.show');
});
