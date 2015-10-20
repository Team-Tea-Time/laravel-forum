<div class="panel panel-default" data-actions>
    <div class="panel-heading">{{ trans('forum::general.actions') }}</div>
    <div class="panel-body">
        <div class="form-group">
            <select name="action" id="action" class="form-control">
                @can ('deleteThreads', $category)
                    @if ($thread->trashed())
                        <option value="restore" data-confirm="true">{{ trans('forum::general.restore') }}</option>
                    @else
                        <option value="delete" data-confirm="true" data-method="delete">{{ trans('forum::general.delete') }}</option>
                    @endif
                @endcan
                @can ('moveThreads', $category)
                    <option value="move">{{ trans('forum::general.move') }}</option>
                @endcan
                @can ('lockThreads', $category)
                    @if ($thread->locked)
                        <option value="unlock">{{ trans('forum::threads.unlock') }}</option>
                    @else
                        <option value="lock">{{ trans('forum::threads.lock') }}</option>
                    @endif
                @endcan
                @can ('pinThreads', $category)
                    @if ($thread->pinned)
                        <option value="unpin">{{ trans('forum::threads.unpin') }}</option>
                    @else
                        <option value="pin">{{ trans('forum::threads.pin') }}</option>
                    @endif
                @endcan
            </select>
        </div>
        <div class="form-group hidden" data-depends="move">
            <label for="destination-category">{{ trans_choice('forum::categories.category', 1) }}</label>
            <select name="destination_category" id="destination-category" class="form-control">
                @include ('forum::category.partials.options')
            </select>
        </div>
        <button type="submit" class="btn btn-default">{{ trans('forum::general.proceed') }}</button>
    </div>
</div>
