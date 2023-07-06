@if ($category->parent !== null)
    @include ('forum.partials.breadcrumb-categories', ['category' => $category->parent])
@endif
<li class=""><a href="{{ Forum::route('category.show', $category) }}" class="text-blue-500">{{ $category->title }}</a></li>
