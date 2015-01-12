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
    @if( $cancel_url )
    <a href="{{ $cancel_url }}" class="button small radius secondary right">Cancel</a>
    @endif
    <button type="submit" class="button small radius">{{ $submit_label }}</button>
  </div>
</div>

{{ Form::close() }}
