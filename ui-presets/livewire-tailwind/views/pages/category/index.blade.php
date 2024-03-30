<div>
    @include ('forum::components.loading-overlay')
    @include ('forum::components.breadcrumbs')

    <h1>{{ trans('forum::general.index') }}</h1>

    <div class="flex">
        <div class="grow">
        </div>
        <div>
            @can ('createCategories')
                <x-forum::link-button
                    :label="trans('forum::categories.create')"
                    icon="squares-plus-outline"
                    :href="Forum::route('category.create')" />
            @endcan
        </div>
    </div>

    @foreach ($categories as $category)
        <livewire:forum::components.category.card :$category :key="$category->id" />
    @endforeach
</div>
