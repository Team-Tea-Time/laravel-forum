<td>
    {{ $thread->replyCount }}
</td>
<td>
    {{ $thread->lastPost->author->username }} ({{ $thread->lastPost->posted }}) &nbsp;
    <a href="{{ URL::to( $thread->lastPostRoute ) }}">{{ trans('forum::base.view_post') }} &raquo;</a>
</td>
