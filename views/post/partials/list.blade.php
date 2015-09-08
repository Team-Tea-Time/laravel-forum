<tr id="post-{{ $post->id }}">
    <td>
        <strong>{!! $post->authorName !!}</strong>
    </td>
    <td>
        @if (!is_null($post->parent))
            <p>
                <strong>
                    {{ trans('forum::general.response_to', ['item' => $post->parent->authorName]) }}
                    (<a href="{{ $post->parent->url }}">{{ trans('forum::posts.view') }}</a>):
                </strong>
            </p>
            <blockquote>
                {!! str_limit(Forum::render($post->parent->content)) !!}
            </blockquote>
        @endif

        {!! Forum::render($post->content) !!}
    </td>
</tr>
<tr>
    <td>
        @if (!$post->trashed())
            @if (Forum::userCan('post.edit', compact('category', 'thread', 'post')))
                <a href="{{ $post->editRoute }}">{{ trans('forum::general.edit') }}</a>
            @endif
            @if (Forum::userCan('api.post.destroy', compact('category', 'thread', 'post')))
                <a href="{{ $post->deleteRoute }}" data-confirm data-method="delete">{{ trans('forum::general.delete') }}</a>
            @endif
        @endif
    </td>
    <td class="text-muted">
        {{ trans('forum::general.posted') }} {{ $post->posted }}
        @if ($post->hasBeenUpdated())
            {{ trans('forum::general.last_updated') }} {{ $post->updated }}
        @endif
        <span class="pull-right">
            <a href="{{ $post->url }}">#{{ $post->id }}</a>
            <span v-if="!locked && !deleted">
                - <a href="{{ $post->replyRoute }}">{{ trans('forum::general.reply') }}</a>
            </span>
            @if (Request::fullUrl() != $post->route)
                <span v-if="!deleted">
                    - <a href="{{ $post->route }}">{{ trans('forum::posts.view') }}</a>
                </span>
            @endif
        </span>
    </td>
</tr>
