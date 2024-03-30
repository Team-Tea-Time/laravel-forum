@foreach ($categories as $category)
    <option value="{{ $category->id }}" {{ isset($disable) && $category->id == $disable || !$category->accepts_threads ? 'disabled' : '' }}>
        @for ($i = 0; $i < $category->depth; ++$i)- @endfor
        {{ $category->title }}
    </option>

    @if ($category->children)
        @include ('forum::components.category.options', ['categories' => $category->children])
    @endif
@endforeach
