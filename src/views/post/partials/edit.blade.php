<form method="POST" action="{{ $form_url }}" class="{{ $form_classes }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="_method" value="PATCH">

    @if ($show_title_field)
        <div class="form-group">
            <label for="title">{{ trans('forum::general.title') }}</label>
            <input type="text" name="title" value="{{ Input::old('title') }}" class="form-control">
        </div>
    @endif

    <div class="form-group">
        <textarea name="content" class="form-control">{{ $post_content }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary">{{ $submit_label }}</button>
    @if ($cancel_url)
        <a href="{{ $cancel_url }}" class="btn btn-default">{{ trans('forum::general.cancel') }}</a>
    @endif
</form>
