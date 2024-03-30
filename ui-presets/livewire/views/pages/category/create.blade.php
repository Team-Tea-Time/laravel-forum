<div>
    @include ('forum::components.loading-overlay')
    @include ('forum::components.breadcrumbs')

    <div class="flex justify-center items-center">
        <div class="grow max-w-screen-md">
            <h1>{{ trans('forum::categories.create') }}</h1>

            <div class="bg-white rounded-md shadow-md my-2 p-6 dark:bg-slate-700">
                <form wire:submit="create">
                    <x-forum::form.input-text
                        id="title"
                        :label="trans('forum::general.title')"
                        wire:model="title" />

                    <x-forum::form.input-text
                        id="description"
                        :label="trans('forum::general.description')"
                        wire:model="description" />

                    <x-forum::form.input-text
                        id="color"
                        :label="trans('forum::general.color_light_mode')"
                        wire:model="color_light_mode"
                        data-coloris />

                    <x-forum::form.input-text
                        id="color"
                        :label="trans('forum::general.color_dark_mode')"
                        wire:model="color_dark_mode"
                        data-coloris />

                    @if ($categories->count() > 0)
                        <x-forum::form.input-select
                            id="parent-category"
                            :label="trans('forum::categories.parent')"
                            wire:model="parent_category">
                            <option value="0">{{ trans('forum::general.none') }}</option>
                            @include ('forum::components.category.options', ['categories' => $categories])
                        </x-forum::form.input-select>
                    @endif

                    <x-forum::form.input-checkbox
                        id="accepts-threads"
                        :label="trans('forum::categories.enable_threads')"
                        wire:model="accepts_threads" />

                    <x-forum::form.input-checkbox
                        id="make-private"
                        :label="trans('forum::categories.make_private')"
                        wire:model="is_private" />

                    <div class="text-center mt-4">
                        <x-forum::button :label="trans('forum::general.create')" type="submit" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
