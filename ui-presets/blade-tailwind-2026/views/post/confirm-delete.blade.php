@extends ('forum::layouts.main', ['breadcrumbs_append' => [trans_choice('forum::posts.delete', 1)]])

@section ('content')
    <section class="forum-hero">
        <div class="forum-hero-kicker">{{ trans_choice('forum::posts.delete', 1) }}</div>
        <h1 class="forum-hero-title">{{ trans_choice('forum::posts.delete', 1) }}</h1>
    </section>

    @include ('forum::post.partials.list', ['post' => $post, 'single' => true])

    <form method="POST" action="{{ Forum::route('post.delete', $post) }}" class="forum-panel mt-5 space-y-5 p-5 sm:p-6">
        @csrf
        @method('DELETE')

        @if (config('forum.general.soft_deletes'))
            <label class="flex items-center gap-3 text-sm font-bold text-slate-700 dark:text-slate-200">
                <input class="forum-check" type="checkbox" name="permadelete" value="1" id="permadelete">
                {{ trans('forum::general.perma_delete') }}
            </label>
        @else
            <p class="forum-muted">{{ trans('forum::general.generic_confirm') }}</p>
        @endif

        <div class="flex justify-end gap-3">
            <x-forum::button-link href="{{ URL::previous() }}" class="forum-button-secondary">{{ trans('forum::general.cancel') }}</x-forum::button-link>
            <x-forum::button type="submit" class="forum-button-danger">{{ trans('forum::general.delete') }}</x-forum::button>
        </div>
    </form>
@stop
