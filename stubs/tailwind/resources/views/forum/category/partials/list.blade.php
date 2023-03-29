<div class="my-4">
    <div class="bg-white shadow rounded-md relative">
        <div class="flex flex-col md:items-start md:flex-row md:justify-between md:gap-4 p-4">
            <div class="md:w-3/6 text-center md:text-left">
                <h5 class="text-lg">
                    <a href="{{ Forum::route('category.show', $category) }}" style="color: {{ $category->color }};">{{ $category->title }}</a>
                </h5>
                <p class="text-gray-500">{{ $category->description }}</p>
            </div>
            <div class="md:w-1/6 flex flex-col items-center md:items-end gap-1">
                @if ($category->accepts_threads)
                    <x-forum.badge style="background: {{ $category->color }};">
                        {{ trans_choice('forum::threads.thread', 2) }}: {{ $category->thread_count }}
                    </x-forum.badge>
                    <x-forum.badge style="background: {{ $category->color }};">
                        {{ trans_choice('forum::posts.post', 2) }}: {{ $category->post_count }}
                    </x-forum.badge>
                @endif
            </div>
            <div class="md:w-2/6 text-gray-500 flex flex-col items-center md:items-end">
                @if ($category->accepts_threads)
                    @if ($category->newestThread)
                        <div>
                            <a href="{{ Forum::route('thread.show', $category->newestThread) }}" class="text-blue-500">{{ $category->newestThread->title }}</a>
                            @include ('forum.partials.timestamp', ['carbon' => $category->newestThread->created_at])
                        </div>
                    @endif
                    @if ($category->latestActiveThread && $category->latestActiveThread->post_count > 1)
                        <div>
                            <a href="{{ Forum::route('thread.show', $category->latestActiveThread->lastPost) }}">Re: {{ $category->latestActiveThread->title }}</a>
                            @include ('forum.partials.timestamp', ['carbon' => $category->latestActiveThread->lastPost->created_at])
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    @if ($category->children->count() > 0)
        <div class="subcategories">
            @foreach ($category->children as $subcategory)
                <div class="bg-white -mt-1 shadow rounded-b-md">
                    <div class="flex flex-col md:items-start md:flex-row md:justify-between md:gap-4 p-4">
                        <div class="md:w-3/6 text-center md:text-left">
                            <a href="{{ Forum::route('category.show', $subcategory) }}" style="color: {{ $subcategory->color }};">{{ $subcategory->title }}</a>
                            <div class="text-muted">{{ $subcategory->description }}</div>
                        </div>
                        <div class="md:w-1/6 flex flex-col items-center md:items-end gap-1">
                            <x-forum.badge style="background: {{ $subcategory->color }};">
                                {{ trans_choice('forum::threads.thread', 2) }}: {{ $subcategory->thread_count }}
                            </x-forum.badge>
                            <x-forum.badge style="background: {{ $subcategory->color }};">
                                {{ trans_choice('forum::posts.post', 2) }}: {{ $subcategory->post_count }}
                            </x-forum.badge>
                        </div>
                        <div class="md:w-2/6 text-gray-500 flex justify-center md:flex-col md:items-end">
                            @if ($subcategory->newestThread)
                                <div>
                                    <a href="{{ Forum::route('thread.show', $subcategory->newestThread) }}">{{ $subcategory->newestThread->title }}</a>
                                    @include ('forum.partials.timestamp', ['carbon' => $subcategory->newestThread->created_at])
                                </div>
                            @endif
                            @if ($subcategory->latestActiveThread && $subcategory->latestActiveThread->post_count > 1)
                                <div>
                                    <a href="{{ Forum::route('thread.show', $subcategory->latestActiveThread->lastPost) }}">Re: {{ $subcategory->latestActiveThread->title }}</a>
                                    @include ('forum.partials.timestamp', ['carbon' => $subcategory->latestActiveThread->lastPost->created_at])
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
