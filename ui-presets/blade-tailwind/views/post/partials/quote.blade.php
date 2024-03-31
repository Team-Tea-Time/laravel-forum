<div class="bg-white rounded-md border mb-2">
    <div class="p-6">
        <div class="flex justify-between flex-row-reverse mb-2">
            <span>
                <a href="{{ Forum::route('thread.show', $post) }}" class="text-gray-500">#{{ $post->sequence }}</a>
            </span>
            <div>
                <strong>{{ $post->authorName }}</strong> <span class="text-gray-500">{{ $post->posted }}</span>
            </div>
        </div>
        {!! \Illuminate\Support\Str::limit(Forum::render($post->content)) !!}
    </div>
</div>
