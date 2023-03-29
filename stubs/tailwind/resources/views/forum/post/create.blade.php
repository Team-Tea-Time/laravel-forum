@extends ('forum.master', ['breadcrumbs_append' => [trans('forum::general.new_reply')]])

@section ('content')
    <div id="create-post">
        <h2 class="text-3xl font-medium my-3">{{ trans('forum::general.new_reply') }} ({{ $thread->title }})</h2>

        @if ($post !== null && !$post->trashed())
            <p>{{ trans('forum::general.replying_to', ['item' => $post->authorName]) }}:</p>

            @include ('forum.post.partials.quote')
        @endif

        <hr class="my-4" />

        <form method="POST" action="{{ Forum::route('post.store', $thread) }}">
            {!! csrf_field() !!}
            @if ($post !== null)
                <input type="hidden" name="post" value="{{ $post->id }}">
            @endif

            <div class="mb-3">
                <x-forum.textarea name="content" class="w-full">{{ old('content') }}</x-forum.textarea>
            </div>

            <div class="flex justify-end items-center gap-4">
                <a href="{{ URL::previous() }}" class="text-blue-500 underline">{{ trans('forum::general.cancel') }}</a>
                <x-forum.button type="submit" class="">{{ trans('forum::general.reply') }}</x-forum.button>
            </div>
        </form>
    </div>
@stop
