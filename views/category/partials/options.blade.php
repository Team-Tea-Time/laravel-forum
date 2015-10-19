@foreach ($categories as $cat)
    <option value="{{ $cat->id }}">
        @for ($i = 0; $i < $cat->depth; $i++)- @endfor
        {{ $cat->title }}
    </option>
    @if ($cat->children)
        @include ('forum::category.partials.options', ['categories' => $cat->children])
    @endif
@endforeach
