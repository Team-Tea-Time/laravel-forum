<div class="panel panel-default" data-actions>
    <div class="panel-heading">
        <span class="glyphicon glyphicon-option-vertical"></span>
        <a href="#" data-toggle="collapse" data-target=".collapse.thread-actions">{{ trans('forum::threads.actions') }}</a>
    </div>
    <div class="collapse thread-actions">
        <div class="panel-body">
            <div class="form-group">
                <label for="action">{{ trans_choice('forum::general.actions', 1) }}</label>
                <select name="action" id="action" class="form-control">
                    @can ('deleteThreads', $category)
                        @if ($thread->trashed())
                            <option value="restore" data-confirm="true">{{ trans('forum::general.restore') }}</option>
                        @else
                            <option value="delete" data-confirm="true" data-method="delete">{{ trans('forum::general.delete') }}</option>
                        @endif
                        <option value="permadelete" data-confirm="true" data-method="delete">{{ trans('forum::general.perma_delete') }}</option>
                    @endcan

                    @if (!$thread->trashed())
                        @can ('moveThreadsFrom', $category)
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
                        @can ('rename', $thread)
                            <option value="rename">{{ trans('forum::general.rename') }}</option>
                        @endcan
                    @endif
                </select>
            </div>
            <div class="form-group hidden" data-depends="move">
                <label for="category-id">{{ trans_choice('forum::categories.category', 1) }}</label>
                <select name="category_id" id="category-id" class="form-control">
                    @include ('forum::category.partials.options', ['hide' => $thread->category])
                </select>
            </div>
            <div class="form-group hidden" data-depends="rename">
                <label for="new-title">{{ trans('forum::general.title') }}</label>
                <input type="text" name="title" value="{{ $thread->title }}" class="form-control">
            </div>
        </div>
        <div class="panel-footer clearfix">
            <button type="submit" class="btn btn-default pull-right">{{ trans('forum::general.proceed') }}</button>
        </div>
    </div>
</div>
