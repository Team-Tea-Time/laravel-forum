@include('forum::partials.breadcrumbs')

@include('forum::partials.action', array('label' => trans('forum::base.new_reply'), 'url' => $thread->postAlias, 'accessModel' => $thread))
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
