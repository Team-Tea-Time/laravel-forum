<div class="forum-panel p-4" data-actions>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="forum-section-title">{{ trans('forum::posts.actions') }}</p>
            <p class="forum-muted mt-1 text-xs">{{ trans_choice('forum::general.actions', 1) }}</p>
        </div>
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-[1fr_auto] sm:items-end">
        <div>
            <label for="action" class="forum-label">{{ trans_choice('forum::general.actions', 1) }}</label>
            <select name="action" id="action" class="forum-select mt-2">
                @can ('deletePosts', $post->thread)
                    @can ('delete', $post)
                        <option value="delete" data-confirm="true" data-method="delete">{{ trans('forum::general.delete') }}</option>
                    @endcan
                @endcan
            </select>
        </div>

        <button type="submit" class="forum-button">{{ trans('forum::general.proceed') }}</button>
    </div>
</div>
