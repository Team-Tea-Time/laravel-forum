@include('forum::partials.pathdisplay', compact('parentCategory', 'category', 'topic'))

@include('forum::partials.errorbox')

@if (isset($prevMessages) && count($prevMessages) > 0)
<p class="lead">
	{{ trans('forum::base.latest_messages') }}
</p>
<table class="table table-index">
	<thead>
		<tr>
			<td>
				{{ trans('forum::base.author') }}
			</td>
			<td>
				{{ trans('forum::base.message') }}
			</td>
		</tr>
	</thead>
	<tbody>
		@foreach ($prevMessages as $message)
			@include('forum::partials.message', compact('message'))
		@endforeach
	</tbody>
</table>
@endif

{{ Form::open(array('url' => $actionUrl, 'class' => 'form-horizontal')) }}
<fieldset>

<legend>{{ trans('forum::base.post_message') }}</legend>
<p class="lead">
	{{ trans('forum::posting_into') }} @include('forum::partials.pathdisplay', compact('parentCategory', 'category', 'topic'))
</p>

<div class="control-group">
	<label class="control-label" for="textarea">{{ trans('forum::base.label_your_message') }}</label>
	<div class="controls">
		{{ Form::textarea('data') }}
	</div>
</div>

<div class="control-group">
	<div class="controls">
		{{ Form::submit(trans('forum::base.send')) }}
	</div>
</div>

</fieldset>
{{ Form::close() }}
