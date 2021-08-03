@component('forum::modal-form')
    @slot('key', 'edit-category')
    @slot('title', trans('forum::general.edit'))
    @slot('route', Forum::route('category.update', $category))
    @slot('method', 'PATCH')

    <div class="mb-3">
        <label for="title">{{ trans('forum::general.title') }}</label>
        <input type="text" name="title" value="{{ old('title') ?? $category->title }}" class="form-control">
    </div>
    <div class="mb-3">
        <label for="description">{{ trans('forum::general.description') }}</label>
        <input type="text" name="description" value="{{ old('description') ?? $category->description }}" class="form-control">
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
            <input class="form-check-input" type="checkbox" name="is_private" id="is-private" value="1" {{ $category->is_private ? 'checked' : '' }}>
            <label class="form-check-label" for="is-private">{{ trans('forum::categories.make_private') }}</label>
        </div>
    </div>
    @include ('forum::category.partials.inputs.color')

    @slot('actions')
        <button type="submit" class="btn btn-primary pull-right">{{ trans('forum::general.save') }}</button>
    @endslot
@endcomponent