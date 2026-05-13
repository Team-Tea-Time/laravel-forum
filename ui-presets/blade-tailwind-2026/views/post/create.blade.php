@extends ('forum::layouts.main', ['breadcrumbs_append' => [trans('forum::general.new_reply')]])

@section ('content')
    <section class="forum-hero">
        <div class="forum-hero-kicker">{{ trans('forum::general.new_reply') }}</div>
        <h1 class="forum-hero-title">{{ $thread->title }}</h1>
    </section>

    @if ($post !== null && !$post->trashed())
        <div class="mb-5">
            <div class="forum-section-title mb-2">{{ trans('forum::general.replying_to', ['item' => $post->authorName]) }}</div>
            @include ('forum::post.partials.quote')
        </div>
    @endif

    <form method="POST" action="{{ Forum::route('post.store', $thread) }}" class="forum-panel space-y-5 p-5 sm:p-6">
        {!! csrf_field() !!}

        @if ($post !== null)
            <input type="hidden" name="post" value="{{ $post->id }}" />
        @endif

        <div>
            <x-forum::label for="content">{{ trans('forum::general.reply') }}</x-forum::label>
            <x-forum::textarea id="content" name="content" class="w-full min-h-60">{{ old('content') }}</x-forum::textarea>
        </div>

        <div class="flex justify-end gap-3">
            <x-forum::button-link href="{{ URL::previous() }}" class="forum-button-secondary">{{ trans('forum::general.cancel') }}</x-forum::button-link>
            <x-forum::button type="submit">{{ trans('forum::general.reply') }}</x-forum::button>
        </div>
    </form>
@stop
