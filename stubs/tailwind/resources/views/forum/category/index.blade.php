{{-- $category is passed as NULL to the master layout view to prevent it from showing in the breadcrumbs --}}
@extends ('forum.master', ['category' => null])

@section ('content')
    <div class="flex flex-row justify-between mb-2">
        <h2 class="grow text-3xl font-bold">{{ trans('forum::general.index') }}</h2>

        @can ('createCategories')
            <x-forum.button type="button" data-open-modal="create-category">
                {{ trans('forum::categories.create') }}
            </x-forum.button>

            @include ('forum.category.modals.create')
        @endcan
    </div>

    @foreach ($categories as $category)
        @include ('forum.category.partials.list', ['titleClass' => 'lead'])
    @endforeach
@stop
