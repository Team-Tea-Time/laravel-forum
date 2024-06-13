<div x-data="editCategory">
    @include ('forum::components.loading-overlay')
    @include ('forum::components.breadcrumbs')

    <div class="flex justify-center items-center">
        <div class="grow max-w-screen-md">
            <h1 class="mb-2">{{ trans('forum::categories.edit') }}</h1>
            <h2 class="mb-4 text-slate-500">{{ $category->title }}</h2>

            <div class="bg-white rounded-md shadow-md my-2 p-6 dark:bg-slate-700">
                <form wire:submit="save">
                    <x-forum::form.input-text
                        id="title"
                        :label="trans('forum::general.title')"
                        wire:model="title" />

                    <x-forum::form.input-text
                        id="description"
                        :label="trans('forum::general.description')"
                        wire:model="description" />

                    <x-forum::form.input-text
                        id="color-light-mode"
                        :label="trans('forum::general.color_light_mode')"
                        wire:model="color_light_mode"
                        data-coloris />

                    <x-forum::form.input-text
                        id="color-dark-mode"
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

                    <div class="flex mt-4">
                        <div class="grow">
                            <x-forum::button
                                intent="danger"
                                :label="trans('forum::general.delete')"
                                @click.prevent="requestDelete" />
                        </div>
                        <div>
                            <x-forum::button :label="trans('forum::general.save')" type="submit" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-forum::modal
        :heading="trans('forum::general.generic_confirm')"
        x-show="showDeleteModal"
        onClose="showDeleteModal = false">
        {{ trans('forum::categories.confirm_nonempty_delete') }}

        <div class="flex flex-wrap mt-6">
            <div class="grow">
                <x-forum::button
                    intent="secondary"
                    :label="trans('forum::general.cancel')"
                    @click="showDeleteModal = false" />
            </div>
            <div>
                <x-forum::button
                    intent="primary"
                    :label="trans('forum::general.proceed')"
                    @click="confirmDelete" />
            </div>
        </div>
    </x-forum::modal>
</div>

@script
<script>
Alpine.data('editCategory', () => {
    return {
        showDeleteModal: false,
        requestDelete(event) {
            this.showDeleteModal = true;
        },
        confirmDelete() {
            $wire.delete();
        }
    }
});
</script>
@endscript
