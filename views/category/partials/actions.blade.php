<div class="panel panel-default" data-actions>
    <div class="panel-heading">
        <span class="glyphicon glyphicon-option-vertical"></span>
        <a href="#" data-toggle="collapse" data-target=".collapse.category-options">{{ trans('forum::categories.actions') }}</a>
    </div>
    <div class="collapse category-options">
        <div class="panel-body">
            <div class="form-group">
                <label for="category-action">{{ trans_choice('forum::general.actions', 1) }}</label>
                <select name="action" id="category-action" class="form-control">
                    @can ('deleteCategories')
                        @if ($category->trashed())
                            <option value="restore" data-confirm="true">{{ trans('forum::general.restore') }}</option>
                        @else
                            <option value="delete" data-confirm="true" data-method="delete">{{ trans('forum::general.delete') }}</option>
                        @endif
                        <option value="permadelete" data-confirm="true" data-method="delete">{{ trans('forum::general.perma_delete') }}</option>
                    @endcan

                    @if (!$category->trashed())
                        @can ('moveCategories')
                            <option value="move">{{ trans('forum::general.move') }}</option>
                            <option value="reorder">{{ trans('forum::general.reorder') }}</option>
                        @endcan
                        @can ('renameCategories')
                            <option value="rename">{{ trans('forum::general.rename') }}</option>
                        @endcan
                    @endif
                </select>
            </div>
            <div class="form-group hidden" data-depends="move">
                <label for="category-id">{{ trans_choice('forum::categories.category', 1) }}</label>
                <select name="category_id" id="category-id" class="form-control">
                    @include ('forum::category.partials.options', ['hide' => $category])
                </select>
            </div>
            <div class="form-group hidden" data-depends="reorder">
                <label for="new-weight">{{ trans('forum::general.weight') }}</label>
                <input type="number" name="weight" value="{{ $category->weight }}" class="form-control">
            </div>
            <div class="form-group hidden" data-depends="rename">
                <label for="new-title">{{ trans('forum::general.title') }}</label>
                <input type="text" name="title" value="{{ $category->title }}" class="form-control">
            </div>
        </div>
        <div class="panel-footer clearfix">
            <button type="submit" class="btn btn-default pull-right">{{ trans('forum::general.proceed') }}</button>
        </div>
    </div>
</div>
