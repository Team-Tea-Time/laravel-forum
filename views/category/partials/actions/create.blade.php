<button type="button" class="btn btn-primary" data-open-modal="create-category">
    {{ trans('forum::categories.create') }}
</button>

<div class="modal fade" tabindex="-1" role="dialog" data-modal="create-category" data-close-modal>
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-sm">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('forum::categories.create') }}</h5>
                <button type="button" class="close" aria-label="Close" data-close-modal>
                    <span aria-hidden="true" data-close-modal>&times;</span>
                </button>
            </div>
            <form action="{{ Forum::route('category.store') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">{{ trans('forum::general.title') }}</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="description">{{ trans('forum::general.description') }}</label>
                        <input type="text" name="description" value="{{ old('description') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="accepts_threads" id="accepts-threads" value="1" {{ old('accepts_threads') ? 'checked' : '' }}>
                            <label class="form-check-label" for="accepts-threads">{{ trans('forum::categories.enable_threads') }}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_private" id="is-private" value="1" {{ old('is_private') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is-private">{{ trans('forum::categories.make_private') }}</label>
                        </div>
                    </div>
                    @include ('forum::category.partials.inputs.color')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-close-modal>{{ trans('forum::general.cancel') }}</button>
                    <button type="submit" class="btn btn-primary pull-right">{{ trans('forum::general.create') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
