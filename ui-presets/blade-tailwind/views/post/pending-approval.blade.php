@extends ('forum::layouts.main', ['breadcrumbs_append' => [trans('forum::posts.pending_approval')]])

@section ('content')
    <div id="pending-approval" data-all-ids="{{ $posts->pluck('id')->toJson() }}" v-cloak>
        <div class="flex flex-col md:flex-row justify-between my-4">
            <h2 class="text-2xl font-semibold">{{ trans('forum::posts.pending_approval') }}</h2>
        </div>

        @if ($posts->isEmpty())
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-500">{{ trans('forum::posts.none_found') }}</p>
            </div>
        @else
            <div class="flex justify-end mb-4">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" v-model="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="text-sm text-gray-700">{{ trans('forum::posts.select_all') }}</span>
                </label>
            </div>

            <div class="space-y-4">
                @foreach ($posts as $post)
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="p-4 border-b">
                            <div class="flex items-start gap-4">
                                <div class="pt-1">
                                    <input type="checkbox" name="posts[]" v-model="selectedIds" :value="{{ $post->id }}" class="post-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                                        <div class="flex-1">
                                            <div class="mt-2 text-sm text-gray-700">
                                                {!! $post->content !!}
                                            </div>
                                            <div class="text-sm text-gray-500 mt-1">
                                                {{ $post->authorName }}
                                                ({{ $post->created_at->diffForHumans() }})
                                            </div>
                                            <div class="mt-2 text-sm">
                                                {{ trans_choice('forum::threads.thread', 1) }}: <a href="{{ Forum::route('thread.show', $post->thread) }}?post={{ $post->id }}" class="text-blue-600 hover:underline">
                                                    {{ $post->thread->title }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex gap-4">
                <button type="button" 
                        :disabled="!selectedIds.length"
                        data-open-modal="delete-posts"
                        class="bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ trans('forum::general.delete_selection') }}
                </button>
                <button type="button" 
                        :disabled="!selectedIds.length"
                        data-open-modal="approve-posts"
                        class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ trans('forum::general.approve_selection') }}
                </button>
            </div>

            @if ($posts->hasPages())
                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif
        
        @component('forum::modal-form')
            @slot('key', 'delete-posts')
            @slot('title', trans('forum::general.delete_selection'))
            @slot('route', Forum::route('forum.bulk.post.delete'))
            @slot('method', 'DELETE')
            @slot('actions')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-600">
                    {{ trans('forum::general.proceed') }}
                </button>
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
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">
                    {{ trans('forum::general.proceed') }}
                </button>
            @endslot

            <p>{{ trans('forum::general.generic_confirm') }}</p>
            <template v-for="id in selectedIds" :key="id">
                <input type="hidden" name="posts[]" :value="id">
            </template>
        @endcomponent
    </div>
@stop
