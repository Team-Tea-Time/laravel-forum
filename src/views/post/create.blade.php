@extends ('forum::master')

@section ('content')
    <h2>{{ trans('forum::general.new_reply') }} ({{ $thread->title }})</h2>

    @if (!is_null($post))
        <h3>{{ trans('forum::general.replying_to') }}...</h3>

        <table class="table">
            <thead>
                <tr>
                    <th class="col-md-2">
                        {{ trans('forum::general.author') }}
                    </th>
                    <th>
                        {{ trans('forum::posts.post') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr id="post-{{ $post->id }}">
                	<td>
                		<strong>{!! $post->authorName !!}</strong>
                	</td>
                	<td>
                		{!! nl2br(e($post->content)) !!}
                	</td>
                </tr>
            </tbody>
        </table>
    @endif

    @include (
        'forum::post.partials.edit',
        [
            'form_url'          => $thread->replyRoute,
            'method'            => 'POST',
            'show_title_field'  => false,
            'submit_label'      => trans('forum::general.reply'),
            'cancel_url'        => $thread->route
        ]
    )
@overwrite
