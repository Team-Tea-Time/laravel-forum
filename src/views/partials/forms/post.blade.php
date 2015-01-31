{{ Form::open( [ 'url' => $form_url, 'class' => $form_classes ] ) }}

@if( $show_title_field )
<div class="control-group">
  <label class="control-label" for="title">{{ trans('forum::base.title') }}</label>
  <div class="controls">
    {{ Form::text('title') }}
  </div>
</div>
@endif

<div class="control-group">
  <div class="controls">
    {{ Form::textarea('content', $post_content, ['class' => 'bbcode editor']) }}
  </div>
</div>

<div class="control-group">
  <div class="controls">
    <button type="submit" class="btn btn-primary">{{ $submit_label }}</button>
    @if( $cancel_url )
    <a href="{{ $cancel_url }}" class="btn btn-default">Cancel</a>
    @endif
  </div>
</div>

{{ Form::close() }}
