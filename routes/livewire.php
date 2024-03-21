<?php

use TeamTeaTime\Forum\Http\Livewire\Pages\CategoryIndex;
use TeamTeaTime\Forum\Http\Livewire\Pages\CategoryShow;

$prefix = config('forum.frontend.route_prefixes');

Route::get('/', CategoryIndex::class);
Route::get($prefix['category'] . '/{category}-{category_slug}', CategoryShow::class)->name('category.show');
Route::get('/thread', CategoryShow::class)->name('thread.show');
