@if (!is_null($category->parent))
    @include ('forum::partials.breadcrumb-categories', ['category' => $category->parent])
@endif
<li><a href="{{ $category->route }}">{{ $category->title }}</a></li>
