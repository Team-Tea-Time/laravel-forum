@extends ('forum::master')

@section ('content')
    @include ('forum::partials.breadcrumbs')

    <div id="post">
        <h2>{{ trans('forum::posts.view') }} ({{ $thread->title }})</h2>

        <a href="{{ $post->url }}" class="btn btn-default">&laquo; {{ trans('forum::threads.view') }}</a>

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
                @include ('forum::post.partials.list', compact('post'))
            </tbody>
        </table>
    </div>
@overwrite
