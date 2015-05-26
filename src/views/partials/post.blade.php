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
		@if ($post->canEdit)
			<a href="{{ $post->editRoute }}">{{ trans('forum::base.edit')}}</a>
		@endif
		@if ($post->canDelete)
			<a href="{{ $post->deleteRoute }}" data-confirm data-method="delete">{{ trans('forum::base.delete') }}</a>
		@endif
	</td>
	<td class="text-muted">
		{{ trans('forum::base.posted_at') }} {{ $post->posted }}
		@if ($post->updated_at != null && $post->created_at != $post->updated_at)
			{{ trans('forum::base.last_update') }} {{ $post->updated }}
		@endif
	</td>
</tr>
