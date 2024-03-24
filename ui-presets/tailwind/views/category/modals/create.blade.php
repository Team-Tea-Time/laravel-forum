@component('forum.modal-form')
    @slot('key', 'create-category')
    @slot('title', trans('forum::categories.create'))
    @slot('route', Forum::route('category.store'))

    <div class="mb-3">
        <x-forum.label for="title">{{ trans('forum::general.title') }}</x-forum.label>
        <x-forum.input type="text" name="title" value="{{ old('title') }}" class="w-full" />
    </div>
    <div class="mb-3">
        <x-forum.label for="description">{{ trans('forum::general.description') }}</x-forum.label>
        <x-forum.input type="text" name="description" value="{{ old('description') }}" class="w-full" />
    </div>
    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="accepts_threads" id="accepts-threads" value="1" {{ old('accepts_threads') ? 'checked' : '' }}>
            <label class="form-check-label" for="accepts-threads">{{ trans('forum::categories.enable_threads') }}</label>
        </div>
    </div>
    <div class="mb-3">
        <div>
            <input type="checkbox" name="is_private" id="is-private" value="1" {{ old('is_private') ? 'checked' : '' }}>
            <label for="is-private">{{ trans('forum::categories.make_private') }}</label>
        </div>
    </div>
    @include ('forum.category.partials.inputs.color')

    @slot('actions')
        <x-forum.button type="submit">{{ trans('forum::general.create') }}</x-forum.button>
    @endslot
@endcomponent
