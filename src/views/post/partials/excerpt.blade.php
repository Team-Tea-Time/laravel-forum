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
