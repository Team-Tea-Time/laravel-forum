@extends ('forum::master')

@section ('content')
    @include ('forum::partials.breadcrumbs')

    <div id="thread">
        <h2>
            @if ($thread->locked)
                [{{ trans('forum::threads.locked') }}]
            @endif
            @if ($thread->pinned)
                [{{ trans('forum::threads.pinned') }}]
            @endif
            {{ $thread->title }}
        </h2>

        @if ($thread->userCanLock || $thread->userCanPin || $thread->userCanDelete)
            <div class="thread-tools dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="thread-actions" data-toggle="dropdown" aria-expanded="true">
                    {{ trans('forum::general.actions') }}
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    @if ($thread->userCanLock)
                        <li><a href="{{ $thread->lockRoute }}" data-method="PATCH">{{ trans('forum::threads.lock') }}</a></li>
                    @endif
                    @if ($thread->userCanPin)
                        <li><a href="{{ $thread->pinRoute }}" data-method="PATCH">{{ trans('forum::threads.pin') }}</a></li>
                    @endif
                    @if ($thread->userCanDelete)
                        <li><a href="{{ $thread->deleteRoute }}" data-confirm data-method="DELETE">{{ trans('forum::threads.delete') }}</a></li>
                    @endif
                </ul>
            </div>
            <hr>
        @endif

        @if ($thread->userCanReply)
            <div class="row">
                <div class="col-xs-4">
                    <div class="btn-group" role="group">
                        <a href="{{ $thread->replyRoute }}" class="btn btn-default">{{ trans('forum::general.new_reply') }}</a>
                        <a href="#quick-reply" class="btn btn-default">{{ trans('forum::general.quick_reply') }}</a>
                    </div>
                </div>
                <div class="col-xs-8 text-right">
                    {!! $thread->pageLinks !!}
                </div>
            </div>
        @endif

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
                @foreach ($thread->postsPaginated as $post)
                    @include ('forum::post.partials.list', compact('post'))
                @endforeach
            </tbody>
        </table>

        {!! $thread->pageLinks !!}

        @if ($thread->userCanReply)
            <h3>{{ trans('forum::general.quick_reply') }}</h3>
            <div id="quick-reply">
                @include (
                    'forum::post.partials.edit',
                    [
                        'form_url'          => $thread->replyRoute,
                        'show_title_field'  => false,
                        'submit_label'      => trans('forum::general.reply')
                    ]
                )
            </div>
        @endif
    </div>
@overwrite
