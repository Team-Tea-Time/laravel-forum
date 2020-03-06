<button type="button" class="btn btn-danger" data-open-modal="delete-category">
    {{ trans('forum::general.delete') }}
</button>

<div class="modal fade" tabindex="-1" role="dialog" data-modal="delete-category" data-close-modal>
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-sm">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('forum::general.delete') }}</h5>
                <button type="button" class="close" aria-label="Close" data-close-modal>
                    <span aria-hidden="true" data-close-modal>&times;</span>
                </button>
            </div>
            <form action="{{ Forum::route('category.delete', $category) }}" method="POST">
                @method('DELETE')
                @csrf

                <div class="modal-body">
                    {{ trans('forum::general.generic_confirm') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-close-modal>{{ trans('forum::general.cancel') }}</button>
                    <button type="submit" class="btn btn-danger pull-right">{{ trans('forum::general.delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>