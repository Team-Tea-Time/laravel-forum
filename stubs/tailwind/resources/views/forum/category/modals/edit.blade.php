@component('forum.modal-form')
    @slot('key', 'edit-category')
    @slot('title', trans('forum::general.edit'))
    @slot('route', Forum::route('category.update', $category))
    @slot('method', 'PATCH')

    <div class="mb-3">
        <x-forum.label for="title">{{ trans('forum::general.title') }}</x-forum.label>
        <x-forum.input type="text" name="title" value="{{ old('title') ?? $category->title }}" class="w-full" />
    </div>
    <div class="mb-3">
        <x-forum.label for="description">{{ trans('forum::general.description') }}</x-forum.label>
        <x-forum.input type="text" name="description" value="{{ old('description') ?? $category->description }}" class="w-full" />
    </div>
    <div class="mb-3">
        <div class="form-check">
            <input type="hidden" name="accepts_threads" value="0" />
            <input class="form-check-input" type="checkbox" name="accepts_threads" id="accepts-threads" value="1" {{ $category->accepts_threads ? 'checked' : '' }}>
            <label class="form-check-label" for="accepts-threads">{{ trans('forum::categories.enable_threads') }}</label>
        </div>
    </div>
    <div class="mb-3">
        <div class="form-check">
            <input type="hidden" name="is_private" value="0" />
            <input class="form-check-input" type="checkbox" name="is_private" id="is-private" value="1" {{ $category->is_private ? 'checked' : '' }} {{ $privateAncestor != null ? 'disabled' : '' }}>
            <label class="form-check-label" for="is-private">{{ trans('forum::categories.make_private') }}</label>
        </div>
    </div>
    @if ($privateAncestor != null)
        <div class="alert alert-primary" role="alert">
            {!! trans('forum::categories.access_controlled_by_private_ancestor', ['category' => "<a href=\"{$privateAncestor->route}\">{$privateAncestor->title}</a>"]) !!}
        </div>
    @endif

    @include ('forum.category.partials.inputs.color')

    @slot('actions')
        <x-forum.button type="submit">{{ trans('forum::general.save') }}</x-forum.button>
    @endslot
@endcomponent
