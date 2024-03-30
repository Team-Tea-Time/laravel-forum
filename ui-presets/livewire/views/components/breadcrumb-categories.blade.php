@if ($category->parent !== null)
    @include ('forum::components.breadcrumb-categories', ['category' => $category->parent])
@endif
<li class="breadcrumb-item"><a href="{{ Forum::route('category.show', $category) }}">{{ $category->title }}</a></li>
