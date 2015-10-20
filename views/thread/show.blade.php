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

        @can ('manageThreads', $category)
            <form action="{{ route('forum.thread.update', $thread->id) }}" method="POST" data-actions-form>
                {!! csrf_field() !!}
                {!! method_field('patch') !!}

                @include ('forum::thread.partials.actions')
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
                    {!! $posts->render() !!}
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

        {!! $posts->render() !!}

        @can ('manageThreads', $thread->category)
            </form>
        @endcan

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
