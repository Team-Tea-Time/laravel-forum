{!! Form::open(['url' => $form_url, 'class' => $form_classes]) !!}

@if ( $show_title_field )
<div class="form-group">
    <label for="title">{{ trans('forum::base.title') }}</label>
    {!! Form::text('title', Input::old('title'), ['class' => 'form-control']) !!}
</div>
@endif

<div class="form-group">
    {!! Form::textarea('content', $post_content, ['class' => 'form-control']) !!}
</div>

<button type="submit" class="btn btn-primary">{{ $submit_label }}</button>
@if ( $cancel_url )
<a href="{{ $cancel_url }}" class="btn btn-default">{{ trans('forum::base.cancel') }}</a>
@endif

{!! Form::close() !!}
