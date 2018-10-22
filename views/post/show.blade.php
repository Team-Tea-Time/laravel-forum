@extends ('forum::master', ['breadcrumb_other' => trans('forum::posts.view')])

@section ('content')
    <div id="post">
        <h2>{{ trans('forum::posts.view') }} ({{ $thread->title }})</h2>

        <a href="{{ Forum::route('thread.show', $thread) }}" class="btn btn-default">&laquo; {{ trans('forum::threads.view') }}</a>

        <table class="table">
            <thead>
                <tr>
                    <th class="col col-md-2">
                        {{ trans('forum::general.author') }}
                    </th>
                    <th>
                        {{ trans_choice('forum::posts.post', 1) }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @include ('forum::post.partials.list', compact('post'))
            </tbody>
        </table>
    </div>
@stop
