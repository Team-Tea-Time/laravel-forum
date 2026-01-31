@extends ('forum::layouts.main', ['breadcrumbs_append' => [trans('forum::threads.pending_approval')]])

@section ('content')
    <div id="pending-approval" data-all-ids="{{ $threads->pluck('id')->toJson() }}" v-cloak>
        <div class="flex flex-col md:flex-row justify-between my-4">
            <h2 class="text-2xl font-semibold">{{ trans('forum::threads.pending_approval') }}</h2>
        </div>

        @if ($threads->isEmpty())
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-500">{{ trans('forum::threads.none_found') }}</p>
            </div>
        @else
            <div class="flex justify-end mb-4">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" v-model="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="text-sm text-gray-700">{{ trans('forum::threads.select_all') }}</span>
                </label>
            </div>

            <div class="space-y-4">
                @foreach ($threads as $thread)
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="p-4 border-b">
                            <div class="flex items-start gap-4">
                                <div class="pt-1">
                                    <input type="checkbox" name="threads[]" :value="{{ $thread->id }}" v-model="selectedIds" class="thread-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                                        <div>
                                            <h3 class="font-medium text-lg">
                                                {{ trans_choice('forum::threads.thread', 1) }}: <a href="{{ Forum::route('thread.show', $thread) }}" class="text-blue-600 hover:underline">
                                                    {{ $thread->title }}
                                                </a>
                                            </h3>
                                            <div class="text-sm text-gray-500">
                                                {{ $thread->authorName }}
                                                ({{ $thread->created_at->diffForHumans() }})
                                            </div>
                                            <div class="mt-2 text-sm text-gray-700">
                                                {!! $thread->firstPost->content !!}
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
                        data-open-modal="delete-threads"
                        class="bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ trans('forum::general.delete_selection') }}
                </button>
                <button type="button" 
                        :disabled="!selectedIds.length"
                        data-open-modal="approve-threads"
                        class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ trans('forum::general.approve_selection') }}
                </button>
            </div>

            @if ($threads->hasPages())
                <div class="mt-4">
                    {{ $threads->links() }}
                </div>
            @endif
        @endif
        
        @component('forum::modal-form')
            @slot('key', 'delete-threads')
            @slot('title', trans('forum::general.delete_selection'))
            @slot('route', Forum::route('forum.bulk.thread.delete'))
            @slot('method', 'DELETE')
            @slot('actions')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-600">
                    {{ trans('forum::general.proceed') }}
                </button>
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
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">
                    {{ trans('forum::general.proceed') }}
                </button>
            @endslot

            <p>{{ trans('forum::general.generic_confirm') }}</p>
            <template v-for="id in selectedIds" :key="id">
                <input type="hidden" name="threads[]" :value="id">
            </template>
        @endcomponent
    </div>
@stop
