<table class="table">
    <thead>
        <tr>
            <th class="col-md-2">
                {{ trans('forum::general.author') }}
            </th>
            <th>
                {{ trans_choice('forum::posts.post', 1) }}
            </th>
        </tr>
    </thead>
    <tbody>
        <tr id="post-{{ $post->id }}">
            <td>
                <strong>{!! $post->authorName !!}</strong>
            </td>
            <td>
                {!! Forum::render($post->content) !!}
            </td>
        </tr>
    </tbody>
</table>
