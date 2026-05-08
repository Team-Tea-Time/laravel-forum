<div id="post-{{ $post->sequence }}" class="post-card my-4" x-data="postCard" data-post="{{ $post->id }}" {{ $selectable ? 'x-on:change=onPostChanged' : '' }}>
    <div class="bg-white shadow-md rounded-lg flex flex-col sm:flex-row items-stretch dark:bg-slate-700 {{ $post->trashed() ? 'opacity-65' : '' }}" :class="classes">
        @if ($showAuthorPane)
            <div class="flex flex-row sm:flex-col w-full sm:w-1/5 px-6 py-4 sm:py-6 border-b sm:border-b-0 sm:border-r border-slate-200 dark:border-slate-600">
                <div class="grow text-lg font-medium truncate">
                    {{ $post->authorName }}
                </div>
                <div>
                    @if (!isset($single) || !$single)
                        <a href="{{ Forum::route('thread.show', $post) }}">#{{ $post->sequence }}</a>
                    @endif
                </div>
            </div>
        @endif
        <div class="grow p-6 w-full sm:w-4/5">
            @if ($showThreadTitle)
                <h2 class="mb-2">
                    Re: <a href="{{ $post->thread->route }}" class="text-category">
                        {{ $post->thread->title }}
                    </a>
                </h2>
            @endif

            @if (isset($post->parent))
                <livewire:forum.components.post.quote :post="$post->parent" />
            @endif

            <div class="dark:text-slate-100">
                @if ($post->sequence != 1 && $post->thread->category->requiresPostApproval() && !$post->isApproved)
                    <div class="mb-2">
                        <livewire:forum.components.pill
                            bg-color="bg-orange-400"
                            text-color="text-orange-950"
                            margin="m-0"
                            icon="clipboard-document-check-mini"
                            :text="trans('forum::general.pending_approval')" />
                    </div>
                @endif

                @if ($post->trashed())
                    @can ('viewTrashedPosts')
                        <div class="mb-4">
                            {!! Forum::render($post->content) !!}
                        </div>
                    @endcan

                    <div>
                        <livewire:forum.components.pill
                            bg-color="bg-zinc-400"
                            text-color="text-zinc-950"
                            margin="mr-2"
                            icon="trash-mini"
                            :text="trans('forum::general.deleted')" />
                    </div>
                @else
                    {!! Forum::render($post->content) !!}
                @endif
            </div>

            <div class="flex flex-col sm:flex-row mt-4">
                <div class="grow text-slate-500">
                    <livewire:forum.components.timestamp :carbon="$post->created_at" />
                    @if ($post->hasBeenUpdated())
                        <span class="mx-1 text-slate-500">•</span>
                        {{ trans('forum::general.last_updated') }} <livewire:forum.components.timestamp :carbon="$post->updated_at" />
                    @endif
                </div>
                @if (!isset($single) || !$single)
                    <div class="text-right sm:text-left mt-2 sm:mt-0">
                        @if (!$post->trashed())
                            <a href="{{ Forum::route('post.show', $post) }}" class="font-medium">
                                {{ trans('forum::general.permalink') }}
                            </a>
                            @can ('edit', $post)
                                <a href="{{ Forum::route('post.edit', $post) }}" class="font-medium ml-2">
                                    {{ trans('forum::general.edit') }}
                                </a>
                            @endcan
                            @can ('reply', $post->thread)
                                <a href="{{ Forum::route('thread.reply', $post->thread) }}?parent_id={{ $post->id }}" class="font-medium ml-2">
                                    {{ trans('forum::general.reply') }}
                                </a>
                            @endcan
                        @endif
                        @if ($selectable)
                            <div class="inline-block ml-4">
                                <x-forum::form.input-checkbox
                                    id=""
                                    :value="$post->id"
                                    @change="onChanged" />
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


@script
<script>
Alpine.data('postCard', () => {
    return {
        classes: 'outline-none',
        onChanged(event) {
            event.stopPropagation();

            if (event.target.checked) {
                this.classes = 'outline outline-blue-500';
            } else {
                this.classes = 'outline-none';
            }

            $dispatch('change', { isSelected: event.target.checked, id: event.target.value });
        }
    }
});
</script>
@endscript
