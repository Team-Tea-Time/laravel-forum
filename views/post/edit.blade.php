@extends ('forum::master', ['breadcrumb_other' => trans('forum::posts.edit')])

@section ('content')
    <div id="edit-post">
        <h2>{{ trans('forum::posts.edit') }} ({{ $thread->title }})</h2>

        <hr>

        @if (!$post->isFirst)
            @can ('delete', $post)
                <form action="{{ Forum::route('post.update', $post) }}" method="POST" data-actions-form>
                    {!! csrf_field() !!}
                    {!! method_field('delete') !!}

                    @include ('forum::post.partials.actions')
                </form>
            @endcan
        @endif

        @if ($post->parent)
            <h3>{{ trans('forum::general.response_to', ['item' => $post->parent->authorName]) }}...</h3>

            @include ('forum::post.partials.excerpt', ['post' => $post->parent])
        @endif

        <form method="POST" action="{{ Forum::route('post.update', $post) }}">
            {!! csrf_field() !!}
            {!! method_field('patch') !!}

            <div class="form-group">
                <textarea name="content" class="form-control">{{ !is_null(old('content')) ? old('content') : $post->content }}</textarea>
            </div>

            <button type="submit" class="btn btn-success pull-right">{{ trans('forum::general.proceed') }}</button>
            <a href="{{ URL::previous() }}" class="btn btn-default">{{ trans('forum::general.cancel') }}</a>
        </form>
    </div>
@stop
