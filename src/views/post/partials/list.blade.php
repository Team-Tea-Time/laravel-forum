<tr id="post-{{ $post->id }}">
	<td>
		<strong>{!! $post->authorName !!}</strong>
	</td>
	<td>
		{!! nl2br(e($post->content)) !!}
	</td>
</tr>
<tr>
	<td>
		@if ($post->userCanEdit)
			<a href="{{ $post->editRoute }}">{{ trans('forum::general.edit')}}</a>
		@endif
		@if ($post->userCanDelete)
			<a href="{{ $post->deleteRoute }}" data-confirm data-method="delete">{{ trans('forum::general.delete') }}</a>
		@endif
	</td>
	<td class="text-muted">
		{{ trans('forum::general.posted_at') }} {{ $post->posted }}
		@if ($post->wasUpdated())
			{{ trans('forum::general.last_updated') }} {{ $post->updated }}
		@endif
		<span class="pull-right">
			<a href="{{ $post->url }}">#{{ $post->id }}</a>
			@if (Request::fullUrl() != $post->route)
				(<a href="{{ $post->route }}">{{ trans('forum::posts.view') }}</a>)
			@endif
		</span>
	</td>
</tr>
