@component('forum::modal-form')
    @slot('key', 'delete-category')
    @slot('title', trans('forum::general.delete'))
    @slot('route', Forum::route('category.delete', $category))
    @slot('method', 'DELETE')

    {{ trans('forum::general.generic_confirm') }}

    @slot('actions')
        <button type="submit" class="btn btn-danger">{{ trans('forum::general.delete') }}</button>
    @endslot
@endcomponent