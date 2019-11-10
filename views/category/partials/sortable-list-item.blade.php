<li class="list-group-item" data-id="{{ $category->id }}">
    <strong style="color: {{ $category->color }};">{{ $category->title }}</strong>
    <span class="float-right text-muted">{{ $category->description }}</span>

    <ul class="sortable">
        @foreach ($category->children as $c)
            @include ('forum::category.partials.sortable-list-item', ['category' => $c])
        @endforeach
    </ul>
</li>