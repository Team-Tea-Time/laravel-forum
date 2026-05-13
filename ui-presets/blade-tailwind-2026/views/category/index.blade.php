{{-- $category is passed as NULL to the master layout view to prevent it from showing in the breadcrumbs --}}
@extends ('forum::layouts.main', ['category' => null])

@section ('content')
    <section class="forum-hero">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="forum-hero-kicker">{{ trans('forum::general.home_title') }}</div>
                <h1 class="forum-hero-title">{{ trans('forum::general.index') }}</h1>
                <p class="forum-hero-copy">Browse active categories, recent replies, and community support threads from one focused workspace.</p>
            </div>

            @can ('createCategories')
                <div class="shrink-0">
                    <x-forum::button type="button" data-open-modal="create-category">
                        <i data-feather="plus" class="h-4 w-4"></i>
                        {{ trans('forum::categories.create') }}
                    </x-forum::button>
                </div>

                @include ('forum::category.modals.create')
            @endcan
        </div>
    </section>

    <section class="space-y-4">
        @foreach ($categories as $category)
            @include ('forum::category.partials.list')
        @endforeach
    </section>
@stop
