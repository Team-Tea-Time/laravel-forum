@if ($category->parent !== null)
    @include ('forum::partials.breadcrumb-categories', ['category' => $category->parent])
@endif
<li><a href="{{ Forum::route('category.show', $category) }}">{{ $category->title }}</a></li>
