<div x-data="unapproved">
    @include ('forum::components.loading-overlay')
    @include ('forum::components.breadcrumbs')

    <h1 class="mb-0">{{ trans('forum::threads.unapproved_title') }}</h1>

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

    <div class="mt-4 text-right">
        <x-forum::button
            id="save"
            :label="trans('forum::general.approve_selection')"
            x-ref="button"
            @click="approveSelection"
            disabled />
    </div>
</div>

@script
<script>
Alpine.data('unapproved', () => {
    return {
        toggledAllThreads: false,
        selectedThreads: [],

        reset() {
            this.toggledAllThreads = false;
            this.selectedThreads = [];
            $refs.button.disabled = true;
        },

        onThreadChanged(event) {
            if (event.detail.isSelected) {
                this.selectedThreads.push(event.detail.id);
            } else {
                this.selectedThreads.splice(this.selectedThreads.indexOf(event.detail.id), 1);
            }

            $refs.button.disabled = this.selectedThreads.length == 0;
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
        }
    }
});
</script>
@endscript
