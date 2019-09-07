<div id="categoryActionsModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Modal body text goes here.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
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
                    @can ('delete', $category)
                        <option value="delete" data-confirm="true" data-method="delete">{{ trans('forum::general.delete') }}</option>
                    @endcan

                    @can ('createCategories')
                        @if ($category->threadsEnabled)
                            <option value="disable-threads">{{ trans('forum::categories.disable_threads') }}</option>
                        @else
                            <option value="enable-threads">{{ trans('forum::categories.enable_threads') }}</option>
                        @endif
                        @if ($category->private)
                            <option value="make-public">{{ trans('forum::categories.make_public') }}</option>
                        @else
                            <option value="make-private">{{ trans('forum::categories.make_private') }}</option>
                        @endif
                    @endcan
                    @can ('moveCategories')
                        <option value="move">{{ trans('forum::general.move') }}</option>
                        <option value="reorder">{{ trans('forum::general.reorder') }}</option>
                    @endcan
                    @can ('renameCategories')
                        <option value="rename">{{ trans('forum::general.rename') }}</option>
                    @endcan
                </select>
            </div>
            <div class="form-group hidden" data-depends="move">
                <label for="category-id">{{ trans_choice('forum::categories.category', 1) }}</label>
                <select name="category_id" id="category-id" class="form-control">
                    <option value="0">({{ trans('forum::general.none') }})</option>
                    @include ('forum::category.partials.options', ['hide' => $category])
                </select>
            </div>
            <div class="form-group hidden" data-depends="reorder">
                <label for="new-weight">{{ trans('forum::general.weight') }}</label>
                <input type="number" name="weight" value="{{ $category->weight }}" class="form-control">
            </div>
            <div class="form-group hidden" data-depends="rename">
                <label for="new-title">{{ trans('forum::general.title') }}</label>
                <input type="text" id="new-title" name="title" value="{{ $category->title }}" class="form-control">
                <label for="new-description">{{ trans('forum::general.description') }}</label>
                <input type="text" id="new-description" name="description" value="{{ $category->description }}" class="form-control">
            </div>
        </div>
        <div class="panel-footer clearfix">
            <button type="submit" class="btn btn-default pull-right">{{ trans('forum::general.proceed') }}</button>
        </div>
    </div>
</div>
