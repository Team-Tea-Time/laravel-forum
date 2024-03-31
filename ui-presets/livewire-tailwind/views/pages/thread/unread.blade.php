<div x-data="unread">
    @include ('forum::components.loading-overlay')
    @include ('forum::components.breadcrumbs')

    <h1 class="mb-0">{{ trans('forum::threads.unread_updated') }}</h1>

    <div class="my-4">
        @foreach ($threads as $thread)
            <livewire:forum::components.thread.card
                :$thread
                :key="$thread->id . $updateKey"
                :selectable="false"
                :show-category="true" />
        @endforeach

        @if ($threads->count() == 0)
            <div class="p-6 border border-slate-300 dark:border-slate-700 rounded-md text-center text-slate-500 text-lg font-medium">
                {{ trans('forum::threads.none_found') }}
            </div>
        @else
            <div class="text-center mt-6">
                <x-forum::button
                    :label="trans('forum::general.mark_read')"
                    icon="book-open-outline"
                    @click="showMarkAsReadModal = true" />
            </div>
        @endif
    </div>

    <x-forum::modal
        :heading="trans('forum::general.mark_read')"
        x-show="showMarkAsReadModal"
        onClose="showMarkAsReadModal = false">
        {{ trans('forum::general.generic_confirm') }}

        <div class="flex flex-wrap mt-6">
            <div class="grow">
                <x-forum::button
                    intent="secondary"
                    :label="trans('forum::general.cancel')"
                    @click="showMarkAsReadModal = false" />
            </div>
            <div>
                <x-forum::button
                    intent="primary"
                    :label="trans('forum::general.proceed')"
                    @click="markAsRead" />
            </div>
        </div>
    </x-forum::modal>
</div>

@script
<script>
Alpine.data('unread', () => {
    return {
        showMarkAsReadModal: false,
        async markAsRead() {
            const alert = await $wire.markAsRead();
            this.showMarkAsReadModal = false;
            $dispatch('alert', alert);
        }
    }
});
</script>
@endscript
