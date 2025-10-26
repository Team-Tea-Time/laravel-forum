@component('forum::modal-form')
    @slot('key', 'create-category')
    @slot('title', trans('forum::categories.create'))
    @slot('route', Forum::route('category.store'))

    <div class="mb-3">
        <label for="title">{{ trans('forum::general.title') }}</label>
        <input type="text" name="title" value="{{ old('title') }}" class="form-control">
    </div>
    <div class="mb-3">
        <label for="description">{{ trans('forum::general.description') }}</label>
        <input type="text" name="description" value="{{ old('description') }}" class="form-control">
    </div>
    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="accepts_threads" id="accepts-threads" value="1" {{ old('accepts_threads') ? 'checked' : '' }}>
            <label class="form-check-label" for="accepts-threads">{{ trans('forum::categories.enable_threads') }}</label>
        </div>
    </div>
    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_private" id="is-private" value="1" {{ old('is_private') ? 'checked' : '' }}>
            <label class="form-check-label" for="is-private">{{ trans('forum::categories.make_private') }}</label>
        </div>
    </div>

    @if (!config('forum.general.content_approval.threads.enable_globally'))
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="thread_approval_enabled" id="thread-approval-enabled" value="1" {{ old('thread_approval_enabled') ? 'checked' : '' }}>
                <label class="form-check-label" for="thread-approval-enabled">{{ trans('forum::categories.enable_thread_approval') }}</label>
            </div>
        </div>
    @endif

    @if (!config('forum.general.content_approval.posts.enable_globally'))
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="post_approval_enabled" id="post-approval-enabled" value="1" {{ old('post_approval_enabled') ? 'checked' : '' }}>
                <label class="form-check-label" for="post-approval-enabled">{{ trans('forum::categories.enable_post_approval') }}</label>
            </div>
        </div>
    @endif

    @include ('forum::category.partials.inputs.color')

    @slot('actions')
        <button type="submit" class="btn btn-primary pull-right">{{ trans('forum::general.create') }}</button>
    @endslot
@endcomponent
