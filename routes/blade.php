<?php

use TeamTeaTime\Forum\Http\Controllers\Blade\{
    Bulk\CategoryController as BulkCategoryController,
    Bulk\PostController as BulkPostController,
    Bulk\ThreadController as BulkThreadController,
    CategoryController,
    PostController,
    ThreadController,
};

$authMiddleware = config('forum.frontend.router.auth_middleware');
$prefix = config('forum.frontend.route_prefixes');

// Standalone routes
Route::get('/', [CategoryController::class, 'index'])->name('index');

Route::get('recent', [ThreadController::class, 'recent'])->name('recent');

Route::get('unread', [ThreadController::class, 'unread'])->name('unread');
Route::patch('unread/mark-as-read', [ThreadController::class, 'markAsRead'])->name('unread.mark-as-read')->middleware($authMiddleware);

Route::get('manage', [CategoryController::class, 'manage'])->name('category.manage')->middleware($authMiddleware);

// Categories
Route::post($prefix['category'] . '/create', [CategoryController::class, 'store'])->name('category.store');
Route::prefix($prefix['category'] . '/{category_id}-{category_slug}')->group(function () use ($prefix, $authMiddleware) {
    Route::get('/', [CategoryController::class, 'show'])->name('category.show');
    Route::patch('/', [CategoryController::class, 'update'])->name('category.update')->middleware($authMiddleware);
    Route::delete('/', [CategoryController::class, 'delete'])->name('category.delete')->middleware($authMiddleware);

    Route::get($prefix['thread'] . '/create', [ThreadController::class, 'create'])->name('thread.create');
    Route::post($prefix['thread'] . '/create', [ThreadController::class, 'store'])->name('thread.store')->middleware($authMiddleware);
});

// Threads
Route::prefix($prefix['thread'] . '/{thread_id}-{thread_slug}')->group(function () use ($prefix, $authMiddleware) {
    Route::get('/', [ThreadController::class, 'show'])->name('thread.show');
    Route::get($prefix['post'] . '/{post_id}', [PostController::class, 'show'])->name('post.show');

    Route::middleware($authMiddleware)->group(function () use ($prefix) {
        Route::patch('/', [ThreadController::class, 'update'])->name('thread.update');
        Route::post('lock', [ThreadController::class, 'lock'])->name('thread.lock');
        Route::post('unlock', [ThreadController::class, 'unlock'])->name('thread.unlock');
        Route::post('pin', [ThreadController::class, 'pin'])->name('thread.pin');
        Route::post('unpin', [ThreadController::class, 'unpin'])->name('thread.unpin');
        Route::post('move', [ThreadController::class, 'move'])->name('thread.move');
        Route::post('restore', [ThreadController::class, 'restore'])->name('thread.restore');
        Route::post('rename', [ThreadController::class, 'rename'])->name('thread.rename');
        Route::delete('/', [ThreadController::class, 'delete'])->name('thread.delete');

        Route::get('reply', [PostController::class, 'create'])->name('post.create');
        Route::post('reply', [PostController::class, 'store'])->name('post.store');
        Route::get($prefix['post'] . '/{post_id}/edit', [PostController::class, 'edit'])->name('post.edit');
        Route::patch($prefix['post'] . '/{post_id}', [PostController::class, 'update'])->name('post.update');
        Route::get($prefix['post'] . '/{post_id}/delete', [PostController::class, 'confirmDelete'])->name('post.confirm-delete');
        Route::get($prefix['post'] . '/{post_id}/restore', [PostController::class, 'confirmRestore'])->name('post.confirm-restore');
        Route::delete($prefix['post'] . '/{post_id}', [PostController::class, 'delete'])->name('post.delete');
        Route::post($prefix['post'] . '/{post_id}/restore', [PostController::class, 'restore'])->name('post.restore');
    });
});

// Bulk actions
Route::prefix('bulk')->middleware($authMiddleware)->name('bulk.')->group(function () {
    // Categories
    Route::post('category/manage', [BulkCategoryController::class, 'manage'])->name('category.manage');

    // Threads
    Route::prefix('thread')->name('thread.')->group(function () {
        Route::post('move', [BulkThreadController::class, 'move'])->name('move');
        Route::post('lock', [BulkThreadController::class, 'lock'])->name('lock');
        Route::post('unlock', [BulkThreadController::class, 'unlock'])->name('unlock');
        Route::post('pin', [BulkThreadController::class, 'pin'])->name('pin');
        Route::post('unpin', [BulkThreadController::class, 'unpin'])->name('unpin');
        Route::delete('/', [BulkThreadController::class, 'delete'])->name('delete');
        Route::post('restore', [BulkThreadController::class, 'restore'])->name('restore');
    });

    // Posts
    Route::prefix('post')->name('post.')->group(function () {
        Route::post('restore', [BulkPostController::class, 'restore'])->name('restore');
        Route::delete('/', [BulkPostController::class, 'delete'])->name('delete');
    });
});
