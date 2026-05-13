@extends ('forum::layouts.main', ['breadcrumbs_append' => [trans('forum::posts.pending_approval')]])

@section ('content')
    <div id="pending-approval" data-all-ids="{{ $posts->pluck('id')->toJson() }}" v-cloak>
        <section class="forum-hero">
            <div class="forum-hero-kicker">{{ trans('forum::general.manage') }}</div>
            <h1 class="forum-hero-title">{{ trans('forum::posts.pending_approval') }}</h1>
            <p class="forum-hero-copy">Review replies that are waiting for moderator approval.</p>
        </section>

        @if ($posts->isEmpty())
            <div class="forum-empty">
                {{ trans('forum::posts.none_found') }}
            </div>
        @else
            <div class="mb-4 flex justify-end">
                <label class="forum-panel-soft inline-flex cursor-pointer items-center gap-3 px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300">
                    <input type="checkbox" v-model="selectAll" class="forum-check">
                    {{ trans('forum::posts.select_all') }}
                </label>
            </div>

            <div class="space-y-3">
                @foreach ($posts as $post)
                    <article class="forum-panel p-5">
                        <div class="flex items-start gap-4">
                            <input type="checkbox" name="posts[]" v-model="selectedIds" :value="{{ $post->id }}" class="forum-check mt-1">
                            <div class="min-w-0 flex-1">
                                <div class="forum-section-title">{{ $post->authorName }} / {{ $post->created_at->diffForHumans() }}</div>
                                <div class="forum-prose mt-2 line-clamp-3">
                                    {!! Forum::render($post->content) !!}
                                </div>
                                <div class="mt-3 text-sm font-bold">
                                    {{ trans_choice('forum::threads.thread', 1) }}:
                                    <a href="{{ Forum::route('thread.show', $post->thread) }}?post={{ $post->id }}" class="no-underline">
                                        {{ $post->thread->title }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-5 flex flex-wrap gap-3">
                <button type="button" :disabled="!selectedIds.length" data-open-modal="delete-posts" class="forum-button forum-button-danger disabled:opacity-50">
                    {{ trans('forum::general.delete_selection') }}
                </button>
                <button type="button" :disabled="!selectedIds.length" data-open-modal="approve-posts" class="forum-button disabled:opacity-50">
                    {{ trans('forum::general.approve_selection') }}
                </button>
            </div>

            @if ($posts->hasPages())
                <div class="mt-4">
                    {{ $posts->links('forum::pagination') }}
                </div>
            @endif
        @endif

        @component('forum::modal-form')
            @slot('key', 'delete-posts')
            @slot('title', trans('forum::general.delete_selection'))
            @slot('route', Forum::route('forum.bulk.post.delete'))
            @slot('method', 'DELETE')
            @slot('actions')
                <x-forum::button type="submit" class="forum-button-danger">{{ trans('forum::general.proceed') }}</x-forum::button>
            @endslot

            <p>{{ trans('forum::general.generic_confirm') }}</p>
            <template v-for="id in selectedIds" :key="id">
                <input type="hidden" name="posts[]" :value="id">
            </template>
        @endcomponent

        @component('forum::modal-form')
            @slot('key', 'approve-posts')
            @slot('title', trans('forum::general.approve_selection'))
            @slot('route', Forum::route('forum.bulk.post.approve'))
            @slot('method', 'POST')
            @slot('actions')
                <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
            @endslot

            <p>{{ trans('forum::general.generic_confirm') }}</p>
            <template v-for="id in selectedIds" :key="id">
                <input type="hidden" name="posts[]" :value="id">
            </template>
        @endcomponent
    </div>
@stop
