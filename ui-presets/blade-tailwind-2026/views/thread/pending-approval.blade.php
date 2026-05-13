@extends ('forum::layouts.main', ['breadcrumbs_append' => [trans('forum::threads.pending_approval')]])

@section ('content')
    <div id="pending-approval" data-all-ids="{{ $threads->pluck('id')->toJson() }}" v-cloak>
        <section class="forum-hero">
            <div class="forum-hero-kicker">{{ trans('forum::general.manage') }}</div>
            <h1 class="forum-hero-title">{{ trans('forum::threads.pending_approval') }}</h1>
            <p class="forum-hero-copy">Review submitted threads before they become part of the public forum.</p>
        </section>

        @if ($threads->isEmpty())
            <div class="forum-empty">
                {{ trans('forum::threads.none_found') }}
            </div>
        @else
            <div class="mb-4 flex justify-end">
                <label class="forum-panel-soft inline-flex cursor-pointer items-center gap-3 px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300">
                    <input type="checkbox" v-model="selectAll" class="forum-check">
                    {{ trans('forum::threads.select_all') }}
                </label>
            </div>

            <div class="space-y-3">
                @foreach ($threads as $thread)
                    <article class="forum-panel p-5">
                        <div class="flex items-start gap-4">
                            <input type="checkbox" name="threads[]" :value="{{ $thread->id }}" v-model="selectedIds" class="forum-check mt-1">
                            <div class="min-w-0 flex-1">
                                <div class="forum-section-title">{{ $thread->authorName }} / {{ $thread->created_at->diffForHumans() }}</div>
                                <a href="{{ Forum::route('thread.show', $thread) }}" class="mt-2 block text-lg font-extrabold no-underline">
                                    {{ $thread->title }}
                                </a>
                                <div class="forum-prose mt-3 line-clamp-3">
                                    {!! Forum::render($thread->firstPost->content) !!}
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-5 flex flex-wrap gap-3">
                <button type="button" :disabled="!selectedIds.length" data-open-modal="delete-threads" class="forum-button forum-button-danger disabled:opacity-50">
                    {{ trans('forum::general.delete_selection') }}
                </button>
                <button type="button" :disabled="!selectedIds.length" data-open-modal="approve-threads" class="forum-button disabled:opacity-50">
                    {{ trans('forum::general.approve_selection') }}
                </button>
            </div>

            @if ($threads->hasPages())
                <div class="mt-4">
                    {{ $threads->links('forum::pagination') }}
                </div>
            @endif
        @endif

        @component('forum::modal-form')
            @slot('key', 'delete-threads')
            @slot('title', trans('forum::general.delete_selection'))
            @slot('route', Forum::route('forum.bulk.thread.delete'))
            @slot('method', 'DELETE')
            @slot('actions')
                <x-forum::button type="submit" class="forum-button-danger">{{ trans('forum::general.proceed') }}</x-forum::button>
            @endslot

            <p>{{ trans('forum::general.generic_confirm') }}</p>
            <template v-for="id in selectedIds" :key="id">
                <input type="hidden" name="threads[]" :value="id">
            </template>
        @endcomponent

        @component('forum::modal-form')
            @slot('key', 'approve-threads')
            @slot('title', trans('forum::general.approve_selection'))
            @slot('route', Forum::route('forum.bulk.thread.approve'))
            @slot('method', 'POST')
            @slot('actions')
                <x-forum::button type="submit">{{ trans('forum::general.proceed') }}</x-forum::button>
            @endslot

            <p>{{ trans('forum::general.generic_confirm') }}</p>
            <template v-for="id in selectedIds" :key="id">
                <input type="hidden" name="threads[]" :value="id">
            </template>
        @endcomponent
    </div>
@stop
