@extends ('forum::master')

@section ('content')
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

        @can ('manageThreads', $thread->category)
            <div class="thread-tools dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="thread-actions" data-toggle="dropdown" aria-expanded="true">
                    {{ trans('forum::general.actions') }}
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li>
                        <a href="{{ $thread->updateRoute }}">
                            {{ $thread->locked ? trans('forum::threads.unlock') : trans('forum::threads.lock') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ $thread->updateRoute }}">
                            {{ $thread->pinned ? trans('forum::threads.unpin') : trans('forum::threads.pin') }}
                        </a>
                    </li>
                    @can ('deleteThreads', $thread->category)
                        <li>
                            <a href="#">
                                {{ $thread->trashed() ? trans('forum::general.restore') : trans('forum::general.delete') }}
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                {{ trans('forum::general.perma_delete') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>
            <hr>
        @endcan

        @can ('reply', $thread)
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
        @endcan

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
                @foreach ($posts as $post)
                    @include ('forum::post.partials.list', compact('post'))
                @endforeach
            </tbody>
        </table>

        {!! $thread->pageLinks !!}

        @can ('reply', $thread)
            <h3>{{ trans('forum::general.quick_reply') }}</h3>
            <div id="quick-reply">
                @include (
                    'forum::post.partials.edit',
                    [
                        'form_url'          => $thread->replyRoute,
                        'show_title_field'  => false,
                        'submit_label'      => trans('forum::general.reply'),
                        'post'              => null
                    ]
                )
            </div>
        @endcan
    </div>
@overwrite
