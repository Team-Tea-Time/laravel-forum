@component('forum.modal-form')
    @slot('key', 'mark-threads-as-read')
    @slot('title', trans('forum::categories.mark_read'))
    @slot('route', Forum::route('unread.mark-as-read'))
    @slot('method', 'PATCH')

    <input type="hidden" name="category_id" value="{{ $category->id }}" />

    <p>{{ trans('forum::general.generic_confirm') }}</p>

    @slot('actions')
        <x-forum.button type="submit">
            {{ trans('forum::general.mark_read') }}
        </x-forum.button>
    @endslot
@endcomponent
