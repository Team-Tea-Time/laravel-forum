<div x-data="pendingApproval">
    @include ('forum::components.loading-overlay')
    @include ('forum::components.breadcrumbs')

    <h1 class="mb-0">{{ trans('forum::threads.pending_approval') }}</h1>

    @if ($threads->count() > 0)
        <div class="flex justify-end">
            <x-forum::form.input-checkbox
                id="toggle-all"
                value=""
                :label="trans('forum::threads.select_all')"
                x-model="toggledAllThreads"
                @click="toggleAllThreads" />
        </div>
    @endif

    <div class="my-4">
        @foreach ($threads as $thread)
            <livewire:forum.components.thread.card
                :$thread
                :key="$thread->id . $updateKey"
                :selectable="true"
                :show-category="true" />
        @endforeach

        @if ($threads->count() == 0)
            <div class="p-6 border border-slate-300 dark:border-slate-700 rounded-md text-center text-slate-500 text-lg font-medium">
                {{ trans('forum::threads.none_found') }}
            </div>
        @endif
    </div>

    <div class="mt-4 flex">
        <div class="flex-1">
            <x-forum::button
                id="delete"
                intent="danger"
                :label="trans('forum::general.delete_selection')"
                x-ref="buttonDelete"
                @click="showDeleteModal = true"
                disabled />
        </div>
        <div>
            <x-forum::button
                id="approve"
                :label="trans('forum::general.approve_selection')"
                x-ref="buttonApprove"
                @click="showApproveModal = true"
                disabled />
        </div>
    </div>

    <x-forum::modal
        :heading="trans('forum::general.delete_selection')"
        x-show="showDeleteModal"
        onClose="showDeleteModal = false">
        {{ trans('forum::general.generic_confirm') }}

        <div class="flex flex-wrap mt-6">
            <div class="grow">
                <x-forum::button
                    intent="secondary"
                    :label="trans('forum::general.cancel')"
                    @click="showDeleteModal = false" />
            </div>
            <div>
                <x-forum::button
                    intent="danger"
                    :label="trans('forum::general.proceed')"
                    @click="deleteSelection" />
            </div>
        </div>
    </x-forum::modal>

    <x-forum::modal
        :heading="trans('forum::general.approve_selection')"
        x-show="showApproveModal"
        onClose="showApproveModal = false">
        {{ trans('forum::general.generic_confirm') }}

        <div class="flex flex-wrap mt-6">
            <div class="grow">
                <x-forum::button
                    intent="secondary"
                    :label="trans('forum::general.cancel')"
                    @click="showApproveModal = false" />
            </div>
            <div>
                <x-forum::button
                    intent="primary"
                    :label="trans('forum::general.proceed')"
                    @click="approveSelection" />
            </div>
        </div>
    </x-forum::modal>
</div>

@script
<script>
Alpine.data('pendingApproval', () => {
    return {
        toggledAllThreads: false,
        selectedThreads: [],
        showDeleteModal: false,
        showApproveModal: false,

        setButtonsDisabled(disabled) {
            $refs.buttonDelete.disabled = disabled;
            $refs.buttonApprove.disabled = disabled;
        },

        reset() {
            this.toggledAllThreads = false;
            this.selectedThreads = [];
            this.setButtonsDisabled(true);
        },

        onThreadChanged(event) {
            if (event.detail.isSelected) {
                this.selectedThreads.push(event.detail.id);
            } else {
                this.selectedThreads.splice(this.selectedThreads.indexOf(event.detail.id), 1);
            }

            this.setButtonsDisabled(this.selectedThreads.length == 0);
        },

        toggleAllThreads(event) {
            this.toggledAllThreads = !this.toggledAllThreads;
            if (!this.toggledAllThreads) this.selectedThreads = [];

            const checkboxes = document.querySelectorAll('[data-thread] input[type=checkbox]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.toggledAllThreads;
                checkbox.dispatchEvent(new Event('change'));
            });
        },

        async deleteSelection() {
            const result = await $wire.delete(this.selectedThreads);
            if (result.type == 'success') this.reset();
            this.showDeleteModal = false;
            $dispatch('alert', result);
        },

        async approveSelection() {
            const result = await $wire.approve(this.selectedThreads);
            if (result.type == 'success') this.reset();
            this.showApproveModal = false;
            $dispatch('alert', result);
        }
    }
});
</script>
@endscript
