<div x-data="editPost">
    @include ('forum::components.loading-overlay')
    @include ('forum::components.breadcrumbs')

    <div class="flex justify-center items-center">
        <div class="grow max-w-screen-lg">
            <h1 class="mb-2">{{ trans('forum::posts.edit') }}</h1>
            <h2 class="mb-4 text-slate-500">Re: {{ $post->thread->title }}</h2>

            <div class="mb-4 text-right">
                <x-forum::link-button
                    :href="$post->route"
                    :label="trans('forum::threads.view')" />
            </div>

            <div class="bg-white rounded-md shadow-md my-2 p-6 dark:bg-slate-700">
                <form wire:submit="save">
                    <x-forum::form.input-textarea
                        id="content"
                        wire:model="content" />

                    <div class="flex mt-6">
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
        {{ trans_choice('forum::posts.confirm_delete', 1) }}

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
Alpine.data('editPost', () => {
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
