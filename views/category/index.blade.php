{{-- $category is passed as NULL to the master layout view to prevent it from showing in the breadcrumbs --}}
@extends ('forum::master', ['category' => null])

@section ('content')
    <div class="d-flex flex-row justify-content-between mb-2">
        <h2 class="flex-grow-1">{{ trans('forum::general.index') }}</h2>

        @can ('createCategories')
            @include ('forum::category.partials.form-create')
        @endcan
    </div>

    @foreach ($categories as $category)
        @include ('forum::category.partials.list', ['titleClass' => 'lead'])

        @if ($category->children)
            <ul class="list-group text-center text-md-left">
                @foreach ($category->children as $subcategory)
                    <li class="list-group-item">
                        <a href="{{ Forum::route('category.show', $subcategory) }}">{{ $subcategory->title }}</a>
                        <span class="badge badge-light">
                            {{ trans_choice('forum::threads.thread', 2) }}: {{ $subcategory->thread_count }}
                        </span>
                        <span class="badge badge-light">
                            {{ trans_choice('forum::posts.post', 2) }}: {{ $subcategory->post_count }}
                        </span>
                    </li>
                @endforeach
            </ul>

            <hr>
        @endif
    @endforeach
@stop
