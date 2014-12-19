@include('forum::partials.pathdisplay')

@include('forum::partials.postbutton', array('post' => trans('forum::base.new_reply'), 'url' => $thread->postAlias, 'accessModel' => $thread))
<table class="table table-index">
	<thead>
		<tr>
			<td>
				{{ trans('forum::base.author') }}
			</td>
			<td>
				{{ trans('forum::base.post') }}
			</td>
		</tr>
	</thead>
	<tbody>
		@foreach ($posts as $post)
			@include('forum::partials.post', compact('post'))
		@endforeach
	</tbody>
</table>
{{ $paginationLinks }}
