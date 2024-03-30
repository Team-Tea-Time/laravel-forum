<div>
    @include ('forum::components.loading-overlay')
    @include ('forum::components.breadcrumbs')

    <div class="flex justify-center items-center">
        <div class="grow max-w-screen-lg">
            <h1 class="mb-2">{{ trans('forum::general.reply') }}</h1>
            <h2 class="text-slate-500">Re: {{ $thread->title }}</h2>

            @if (isset($parent) && !$parent->trashed())
                <h3 class="mt-4 text-slate-500">{{ trans('forum::general.replying_to', ['item' => $parent->authorName]) }}</h3>
                <livewire:forum::components.post.card :post="$parent" :single="true" :show-author-pane="false" />
            @endif

            <div class="bg-white rounded-md shadow-md my-2 p-6 dark:bg-slate-700">
                <form wire:submit="reply">
                    <x-forum::form.input-textarea
                        id="content"
                        wire:model="content" />

                    <div class="flex mt-6">
                        <div class="grow">
                            <x-forum::link-button
                                href="{{ URL::previous() }}"
                                intent="secondary"
                                label="{{ trans('forum::general.cancel') }}" />
                        </div>
                        <div class="grow text-right">
                            <x-forum::button :label="trans('forum::general.reply')" type="submit" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
