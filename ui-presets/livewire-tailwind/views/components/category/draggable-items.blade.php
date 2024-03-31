@foreach ($categories as $category)
    <li class="font-medium border border-slate-300 dark:border-slate-600 rounded-md text-lg p-4 my-2 cursor-move" data-id="{{ $category->id }}">
        <span class="flex">
            <span class="text-slate-400 mr-1" style="margin-left: -8px">
                @include ('forum::components.icons.drag-area-vertical')
            </span>
            <span class="grow select-none" data-title>{{ $category->title }}</span>
            <a href="{{ Forum::route('category.edit', $category) }}">
                {{ trans('forum::general.edit') }}
            </a>
        </span>
        @if (count($category->children) > 0)
            <ol data-id="{{ $category->id }}">
                @include ('forum::components.category.draggable-items', ['categories' => $category->children])
            </ol>
        @endif
    </li>
@endforeach
