@foreach ($categories as $cat)
    @if ($cat->id != $category->id)
        <option value="{{ $cat->id }}">
            @for ($i = 0; $i < $cat->depth; $i++)- @endfor
            {{ $cat->title }}
        </option>
    @endif
    @if ($cat->children)
        @include ('forum::category.partials.options', ['categories' => $cat->children])
    @endif
@endforeach
