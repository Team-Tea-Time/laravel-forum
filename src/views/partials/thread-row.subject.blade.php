<td>
    <a href="{{ $thread->route }}">
        <span class="pull-right">
            @if($thread->locked)
                <span class="label label-danger">{{ trans('forum::base.locked') }}</span>
            @endif
            @if($thread->pinned)
                <span class="label label-info">{{ trans('forum::base.pinned') }}</span>
            @endif
            @if($thread->userReadStatus)
                <span class="label label-primary">{{ trans($thread->userReadStatus) }}</span>
            @endif
        </span>
    </a>
</td>
