<div class="panel panel-default" data-actions>
    <div class="panel-heading">
        <span class="glyphicon glyphicon-option-vertical"></span>
        <a href="#" data-toggle="collapse" data-target=".collapse">{{ trans('forum::categories.actions') }}</a>
    </div>
    <div class="collapse">
        <div class="panel-body">
            <div class="form-group">
                <label for="category-action">{{ trans_choice('forum::general.actions', 1) }}</label>
                <select name="action" id="category-action" class="form-control">
                    @can ('deleteCategories')
                        <option value="delete" data-confirm="true" data-method="delete">{{ trans('forum::general.delete') }}</option>
                        <option value="permadelete" data-confirm="true" data-method="delete">{{ trans('forum::general.perma_delete') }}</option>
                    @endcan
                    @can ('moveCategories')
                        <option value="move">{{ trans('forum::general.move') }}</option>
                    @endcan
                    @can ('renameCategories')
                        <option value="rename">{{ trans('forum::general.rename') }}</option>
                    @endcan
                </select>
            </div>
            <div class="form-group hidden" data-depends="move">
                <label for="destination-category">{{ trans_choice('forum::categories.category', 1) }}</label>
                <select name="destination_category" id="destination-category" class="form-control">
                    @include ('forum::category.partials.options')
                </select>
            </div>
            <div class="form-group hidden" data-depends="rename">
                <label for="new-title">{{ trans('forum::general.title') }}</label>
                <input name="title" value="{{ $category->title }}" class="form-control">
            </div>
        </div>
        <div class="panel-footer clearfix">
            <button type="submit" class="btn btn-default pull-right">{{ trans('forum::general.proceed') }}</button>
        </div>
    </div>
</div>
