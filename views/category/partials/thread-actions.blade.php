<div class="panel panel-default hidden" data-actions>
    <div class="panel-heading">{{ trans('forum::general.with_selection') }}</div>
    <div class="panel-body">
        <div class="form-group">
            <label for="action">{{ trans('forum::general.actions') }}</label>
            <select name="action" id="action" class="form-control">
                @can ('deleteThreads', $category)
                    <option value="delete" data-confirm="true" data-method="DELETE">{{ trans('forum::general.delete') }}</option>
                @endcan
                @can ('moveThreads', $category)
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
            <label for="move-category">{{ trans_choice('forum::categories.category', 1) }}</label>
            <select name="category" id="move-category" class="form-control">
                @include ('forum::category.partials.options')
            </select>
        </div>
        <button type="submit" class="btn btn-default">{{ trans('forum::general.proceed') }}</button>
    </div>
</div>
