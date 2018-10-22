<div class="panel panel-default hidden" data-actions data-bulk-actions>
    <div class="panel-heading">{{ trans('forum::general.with_selection') }}</div>
    <div class="panel-body">
        <div class="form-group">
            <label for="thread-action">{{ trans_choice('forum::general.actions', 1) }}</label>
            <select name="action" id="thread-action" class="form-control">
                <option value="delete" data-confirm="true" data-method="delete">{{ trans('forum::general.delete') }}</option>
                <option value="restore" data-confirm="true">{{ trans('forum::general.restore') }}</option>
                <option value="permadelete" data-confirm="true" data-method="delete">{{ trans('forum::general.perma_delete') }}</option>
            </select>
        </div>
    </div>
    <div class="panel-footer clearfix">
        <button type="submit" class="btn btn-default pull-right">{{ trans('forum::general.proceed') }}</button>
    </div>
</div>
