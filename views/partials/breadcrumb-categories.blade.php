@if (!is_null($category->parent))
    @include ('forum::partials.breadcrumb-categories', ['category' => $category->parent])
@endif
<li class="breadcrumb-item"><a href="{{ Forum::route('category.show', $category) }}">{{ $category->title }}</a></li>
