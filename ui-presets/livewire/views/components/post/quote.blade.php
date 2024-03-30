<div class="border border-slate-200 rounded-md p-6 mb-4 dark:border-slate-600">
    {!! Forum::render($post->content) !!}
    <div class="flex mt-4">
        <div class="grow">
            <span class="font-medium">
                {{ $post->authorName }}
            </span>
            <span class="text-slate-500">
                <livewire:forum::components.timestamp :carbon="$post->updated_at" />
            </span>
        </div>
        <div>
            <a href="{{ Forum::route('thread.show', $post) }}">#{{ $post->sequence }}</a>
        </div>
    </div>
</div>
