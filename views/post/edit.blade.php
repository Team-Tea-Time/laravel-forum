@extends ('forum::master', ['breadcrumbs_append' => [trans('forum::posts.edit')]])

@section ('content')
    <div id="edit-post">
        <h2>{{ trans('forum::posts.edit') }} ({{ $thread->title }})</h2>

        <hr>

        @if (! $post->isFirst)
            @can ('delete', $post)
                <form action="{{ Forum::route('post.update', $post) }}" method="POST" data-actions-form>
                    @csrf
                    @method('DELETE')

                    @include ('forum::post.partials.actions')
                </form>
            @endcan
        @endif

        @if ($post->parent)
            <h3>{{ trans('forum::general.response_to', ['item' => $post->parent->authorName]) }}...</h3>

            @include ('forum::post.partials.excerpt', ['post' => $post->parent])
        @endif

        <form method="POST" action="{{ Forum::route('post.update', $post) }}">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <textarea name="content" class="form-control">{{ !is_null(old('content')) ? old('content') : $post->content }}</textarea>
            </div>

            <div class="text-right">
                <a href="{{ URL::previous() }}" class="btn btn-link">{{ trans('forum::general.cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ trans('forum::general.save') }}</button>
            </div>
        </form>
    </div>
@stop
