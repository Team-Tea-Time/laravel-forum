<div class="panel panel-default hidden" data-actions data-bulk-actions>
    <div class="panel-heading">{{ trans('forum::general.with_selection') }}</div>
    <div class="panel-body">
        <div class="form-group">
            <label for="thread-action">{{ trans_choice('forum::general.actions', 1) }}</label>
            <select name="action" id="thread-action" class="form-control">
                @can ('deleteThreads', $category)
                    <option value="delete" data-confirm="true" data-method="delete">{{ trans('forum::general.delete') }}</option>
                    <option value="restore" data-confirm="true">{{ trans('forum::general.restore') }}</option>
                    <option value="permadelete" data-confirm="true" data-method="delete">{{ trans('forum::general.perma_delete') }}</option>
                @endcan
                @can ('moveThreadsFrom', $category)
                    <option value="move">{{ trans('forum::general.move') }}</option>
                @endcan
                @can ('lockThreads', $category)
                    <option value="lock">{{ trans('forum::threads.lock') }}</option>
                    <option value="unlock">{{ trans('forum::threads.unlock') }}</option>
                @endcan
                @can ('pinThreads', $category)
                    <option value="pin">{{ trans('forum::threads.pin') }}</option>
                    <option value="unpin">{{ trans('forum::threads.unpin') }}</option>
                @endcan
            </select>
        </div>
        <div class="form-group hidden" data-depends="move">
            <label for="category-id">{{ trans_choice('forum::categories.category', 1) }}</label>
            <select name="category_id" id="category-id" class="form-control">
                @include ('forum::category.partials.options', ['hide' => $category])
            </select>
        </div>
    </div>
    <div class="panel-footer clearfix">
        <button type="submit" class="btn btn-default pull-right">{{ trans('forum::general.proceed') }}</button>
    </div>
</div>
