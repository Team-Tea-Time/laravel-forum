<div x-data="unapproved">
    @include ('forum::components.loading-overlay')
    @include ('forum::components.breadcrumbs')

    <h1 class="mb-0">{{ trans('forum::posts.unapproved') }}</h1>

    <div class="my-4">
        @foreach ($posts as $post)
            <livewire:forum::components.post.card
                :$post
                :key="$post->id . $updateKey"
                :selectable="false"
                :show-category="true" />
        @endforeach

        @if ($threads->count() == 0)
            <div class="p-6 border border-slate-300 dark:border-slate-700 rounded-md text-center text-slate-500 text-lg font-medium">
                {{ trans('forum::posts.none_found') }}
            </div>
        @endif
    </div>
</div>

@script
<script>
Alpine.data('unapproved', () => {
    return {
        toggledAllThreads: false,
        selectedThreads: [],
        toggleAllThreads(event) {
            this.toggledAllThreads = !this.toggledAllThreads;
            if (!this.toggledAllThreads) this.selectedThreads = [];
            const checkboxes = document.querySelectorAll('[data-thread] input[type=checkbox]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.toggledAllThreads;
                checkbox.dispatchEvent(new Event('change'));
            });
        }
    }
});
</script>
@endscript
