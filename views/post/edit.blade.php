@extends ('forum::master', ['breadcrumb_other' => trans('forum::posts.edit')])

@section ('content')
    <h2>{{ trans('forum::posts.edit') }} ({{ $thread->title }})</h2>

    @if ($post->parent)
        <h3>{{ trans('forum::general.response_to', ['item' => $post->parent->authorName]) }}...</h3>

        @include ('forum::post.partials.excerpt', ['post' => $post->parent])
    @endif

    <form method="POST" action="{{ route('forum.post.update', $post->id) }}">
        {!! csrf_field() !!}
        {!! method_field('patch') !!}

        <div class="form-group">
            <textarea name="content" class="form-control">{{ !is_null(old('content')) ? old('content') : $post->content }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">{{ trans('forum::general.reply') }}</button>
        <a href="{{ URL::previous() }}" class="btn btn-default">{{ trans('forum::general.cancel') }}</a>
    </form>
@overwrite
