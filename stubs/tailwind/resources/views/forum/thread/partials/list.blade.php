<div class="bg-white  {{ $thread->pinned ? 'pinned' : '' }} {{ $thread->locked ? 'locked' : '' }} {{ $thread->trashed() ? 'deleted' : '' }}" :class="{ 'border border-blue-500': selectedThreads.includes({{ $thread->id }}) }">
    <div class="flex flex-col md:items-start md:flex-row md:justify-between md:gap-4 p-4">
        <div class="md:w-3/6 text-center md:text-left">
            <span class="lead">
                <a href="{{ Forum::route('thread.show', $thread) }}" @if (isset($category))style="color: {{ $category->color }};"@endif class="text-lg">{{ $thread->title }}</a>
            </span>
            <br>
            {{ $thread->authorName }} <span class="text-gray-500">@include ('forum.partials.timestamp', ['carbon' => $thread->created_at])</span>

            @if (! isset($category))
                <br>
                <a href="{{ Forum::route('category.show', $thread->category) }}" style="color: {{ $thread->category->color }};">{{ $thread->category->title }}</a>
            @endif
        </div>
        <div class="md:w-1/6 flex flex-wrap justify-center items-center md:justify-end gap-1">
            @if ($thread->pinned)
                <x-forum.badge type="info">{{ trans('forum::threads.pinned') }}</x-forum.badge>
            @endif
            @if ($thread->locked)
                <x-forum.badge type="warning">{{ trans('forum::threads.locked') }}</x-forum.badge>
            @endif
            @if ($thread->userReadStatus !== null && ! $thread->trashed())
                <x-forum.badge type="success">{{ trans($thread->userReadStatus) }}</x-forum.badge>
            @endif
            @if ($thread->trashed())
                <x-forum.badge type="danger">{{ trans('forum::general.deleted') }}</x-forum.badge>
            @endif
            <x-forum.badge :style="(isset($category) && $category->color) ? 'background: '.$category->color .';' : null">
                {{ trans('forum::general.replies') }}:
                {{ $thread->reply_count }}
            </x-forum.badge>
        </div>

        @if ($thread->lastPost)
            <div class="md:w-2/6 text-gray-500 flex justify-center md:flex-col md:items-end">
                <a href="{{ Forum::route('thread.show', $thread->lastPost) }}" class="text-blue-500">{{ trans('forum::posts.view') }} &raquo;</a>
                <div>
                    <span>{{ $thread->lastPost->authorName }}</span>
                    <span class="text-gray-500">@include ('forum.partials.timestamp', ['carbon' => $thread->lastPost->created_at])</span>
                </div>
            </div>
        @endif

        @if (isset($category) && isset($selectableThreadIds) && in_array($thread->id, $selectableThreadIds))
            <div class="" style="flex: 0;">
                <input type="checkbox" name="threads[]" :value="{{ $thread->id }}" v-model="selectedThreads">
            </div>
        @endif
    </div>
</div>
