<tr id="post-{{ $post->id }}">
	<td>
		<strong>{!! $post->authorName !!}</strong>
	</td>
	<td>
		@if (!is_null($post->parent))
			<p>
				<strong>
					{{ trans('forum::general.response_to') }}
					{{ $post->parent->authorName }}
					(<a href="{{ $post->parent->url }}">{{ trans('forum::posts.view') }}</a>):
				</strong>
			</p>
			<blockquote>
				{!! str_limit(nl2br(e($post->parent->content))) !!}
			</blockquote>
		@endif

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
		{{ trans('forum::general.posted') }} {{ $post->posted }}
		@if ($post->wasUpdated())
			{{ trans('forum::general.last_updated') }} {{ $post->updated }}
		@endif
		<span class="pull-right">
			<a href="{{ $post->url }}">#{{ $post->id }}</a>
			 - <a href="{{ $post->replyRoute }}">{{ trans('forum::general.reply') }}</a>
			@if (Request::fullUrl() != $post->route)
				- <a href="{{ $post->route }}">{{ trans('forum::posts.view') }}</a>
			@endif
		</span>
	</td>
</tr>
