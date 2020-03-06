<button type="button" class="btn btn-secondary" data-open-modal="edit-category">
    {{ trans('forum::general.edit') }}
</button>

<div class="modal fade" tabindex="-1" role="dialog" data-modal="edit-category" data-close-modal>
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-sm">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('forum::general.edit') }}</h5>
                <button type="button" class="close" aria-label="Close" data-close-modal>
                    <span aria-hidden="true" data-close-modal>&times;</span>
                </button>
            </div>
            <form action="{{ Forum::route('category.update', $category) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">{{ trans('forum::general.title') }}</label>
                        <input type="text" name="title" value="{{ old('title') ?? $category->title }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="description">{{ trans('forum::general.description') }}</label>
                        <input type="text" name="description" value="{{ old('description') ?? $category->description }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="accepts_threads" id="accepts-threads" value="1" {{ $category->accepts_threads ? 'checked' : '' }}>
                            <label class="form-check-label" for="accepts-threads">{{ trans('forum::categories.enable_threads') }}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_private" id="is-private" value="1" {{ $category->is_private ? 'checked' : '' }}>
                            <label class="form-check-label" for="is-private">{{ trans('forum::categories.make_private') }}</label>
                        </div>
                    </div>
                    @include ('forum::category.partials.inputs.color')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-close-modal>{{ trans('forum::general.cancel') }}</button>
                    <button type="submit" class="btn btn-primary pull-right">{{ trans('forum::general.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
