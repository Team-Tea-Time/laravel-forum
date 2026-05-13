@extends ('forum::layouts.main', ['breadcrumbs_append' => [trans('forum::posts.edit')]])

@section ('content')
    <section class="forum-hero">
        <div class="forum-hero-kicker">{{ trans('forum::posts.edit') }}</div>
        <h1 class="forum-hero-title">{{ $thread->title }}</h1>
    </section>

    @if ($post->parent)
        <div class="mb-5">
            <div class="forum-section-title mb-2">{{ trans('forum::general.response_to', ['item' => $post->parent->authorName]) }}</div>
            @include ('forum::post.partials.list', ['post' => $post->parent, 'single' => true])
        </div>
    @endif

    <form method="POST" action="{{ Forum::route('post.update', $post) }}" class="forum-panel space-y-5 p-5 sm:p-6">
        @csrf
        @method('PATCH')

        <div>
            <x-forum::label for="content">{{ trans_choice('forum::posts.post', 1) }}</x-forum::label>
            <x-forum::textarea id="content" name="content" class="w-full min-h-60">{{ old('content') !== null ? old('content') : $post->content }}</x-forum::textarea>
        </div>

        <div class="flex justify-end gap-3">
            <x-forum::button-link href="{{ URL::previous() }}" class="forum-button-secondary">{{ trans('forum::general.cancel') }}</x-forum::button-link>
            <x-forum::button type="submit">{{ trans('forum::general.save') }}</x-forum::button>
        </div>
    </form>
@stop
