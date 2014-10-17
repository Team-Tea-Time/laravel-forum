@include('forum::partials.pathdisplay')

@include('forum::partials.postbutton', array('message' => trans('forum::base.new_reply'), 'url' => $topic->postUrl, 'accessModel' => $topic))
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
		@foreach ($messages as $message)
			@include('forum::partials.message', compact('message'))
		@endforeach
	</tbody>
</table>
{{ $paginationLinks }}
