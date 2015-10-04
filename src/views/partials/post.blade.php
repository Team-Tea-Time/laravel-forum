<tr id="post-{{ $post->id }}">
	<td>
		<strong>{!! $post->authorName !!}</strong>
	</td>
	<td colspan="2">
		{!! $post->decodedContent !!}
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
	<td class="text-right">
		@if($thread->canReply && config('forum.preferences.bbcode.enabled'))
			<div class="btn-group-xs" role="group">
				<a href="{{ $post->replyRoute }}" class="btn btn-default btn-xs">{{ trans('forum::base.quote') }}</a>
				<a href="#quick-reply" data-forumquickquote="{{ $post->id }}" class="btn btn-default btn-xs">{{ trans('forum::base.quick_quote') }}</a>
			</div>
			<div class="hidden" id="forumPostQuote-{{ $post->id }}">{{ $post->quote }}</div>
		@endif
	</td>
</tr>
