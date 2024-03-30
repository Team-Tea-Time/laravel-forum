<div>
    @include ('forum::components.loading-overlay')
    @include ('forum::components.breadcrumbs')

    <div class="flex justify-center items-center">
        <div class="grow max-w-screen-xl">
            <h1 class="mb-2">{{ trans('forum::posts.view') }}</h1>
            <h2 class="text-slate-500">Re: {{ $post->thread->title }}</h2>

            <div class="text-right">
                <x-forum::link-button :href="$post->route" :label="trans('forum::threads.view')" />
            </div>

            <livewire:forum::components.post.card :$post :single="true" />
        </div>
    </div>
</div>
