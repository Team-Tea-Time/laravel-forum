{{-- $category is passed as NULL to the master layout view to prevent it from showing in the breadcrumbs --}}
@extends ('forum::master', ['category' => null])

@section ('content')
    @can ('createCategories')
        @include ('forum::category.partials.form-create')
    @endcan

    <h2>{{ trans('forum::general.index') }}</h2>

    @foreach ($categories as $category)
        <table class="table table-index">
            <thead>
                <tr>
                    <th>{{ trans_choice('forum::categories.category', 1) }}</th>
                    <th class="col-md-2">{{ trans_choice('forum::threads.thread', 2) }}</th>
                    <th class="col-md-2">{{ trans_choice('forum::posts.post', 2) }}</th>
                    <th class="col-md-2">{{ trans('forum::threads.newest') }}</th>
                    <th class="col-md-2">{{ trans('forum::posts.last') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @include ('forum::category.partials.list', ['titleClass' => 'lead'])
                </tr>
                @if (!$category->children->isEmpty())
                    <tr>
                        <th colspan="5">{{ trans('forum::categories.subcategories') }}</th>
                    </tr>
                    @foreach ($category->children as $subcategory)
                        @include ('forum::category.partials.list', ['category' => $subcategory])
                    @endforeach
                @endif
            </tbody>
        </table>
    @endforeach
@stop
