<div x-data="pendingApproval">
    @include ('forum::components.loading-overlay')
    @include ('forum::components.breadcrumbs')

    <h1 class="mb-0">{{ trans('forum::threads.pending_approval') }}</h1>

    <div class="flex justify-end">
        <x-forum::form.input-checkbox
            id="toggle-all"
            value=""
            :label="trans('forum::threads.select_all')"
            x-model="toggledAllThreads"
            @click="toggleAllThreads" />
    </div>

    <div class="my-4">
        @foreach ($threads as $thread)
            <livewire:forum::components.thread.card
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
                @click="deleteSelection"
                disabled />
        </div>
        <div>
            <x-forum::button
                id="approve"
                :label="trans('forum::general.approve_selection')"
                x-ref="buttonApprove"
                @click="approveSelection"
                disabled />
        </div>
    </div>
</div>

@script
<script>
Alpine.data('pendingApproval', () => {
    return {
        toggledAllThreads: false,
        selectedThreads: [],
        confirmMessage: "{{ trans('forum::general.generic_confirm') }}",

        setButtonsDisabled(disabled) {
            $refs.buttonApprove.disabled = disabled;
            $refs.buttonDelete.disabled = disabled;
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

        async approveSelection() {
            const result = await $wire.approve(this.selectedThreads);
            if (result.type == 'success') this.reset();
            $dispatch('alert', result);
        },

        async deleteSelection() {
            if (!confirm(this.confirmMessage)) return;
            const result = await $wire.delete(this.selectedThreads);
            if (result.type == 'success') this.reset();
            $dispatch('alert', result);
        }
    }
});
</script>
@endscript
