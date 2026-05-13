@extends ('forum::layouts.main', ['breadcrumbs_append' => [trans('forum::threads.new_thread')]])

@section ('content')
    <section class="forum-hero">
        <div class="forum-hero-kicker">{{ $category->title }}</div>
        <h1 class="forum-hero-title">{{ trans('forum::threads.new_thread') }}</h1>
        <p class="forum-hero-copy">Start a focused discussion in this category.</p>
    </section>

    <form method="POST" action="{{ Forum::route('thread.store', $category) }}" class="forum-panel space-y-5 p-5 sm:p-6">
        @csrf

        <div>
            <x-forum::label for="title">{{ trans('forum::general.title') }}</x-forum::label>
            <x-forum::input id="title" type="text" name="title" value="{{ old('title') }}" class="w-full" />
        </div>

        <div>
            <x-forum::label for="content">{{ trans_choice('forum::posts.post', 1) }}</x-forum::label>
            <x-forum::textarea id="content" name="content" class="w-full min-h-60">{{ old('content') }}</x-forum::textarea>
        </div>

        <div class="flex justify-end gap-3">
            <x-forum::button-link href="{{ URL::previous() }}" class="forum-button-secondary">{{ trans('forum::general.cancel') }}</x-forum::button-link>
            <x-forum::button type="submit">{{ trans('forum::general.create') }}</x-forum::button>
        </div>
    </form>
@stop
