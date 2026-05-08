<div x-data="pendingApproval">
    @include ('forum::components.loading-overlay')
    @include ('forum::components.breadcrumbs')

    <h1 class="mb-0">{{ trans('forum::posts.pending_approval') }}</h1>

    @if ($posts->count() > 0)
        <div class="flex justify-end">
            <x-forum::form.input-checkbox
                id="toggle-all"
                value=""
                :label="trans('forum::posts.select_all')"
                x-model="toggledAllPosts"
                @click="toggleAllPosts" />
        </div>
    @endif

    <div class="my-4">
        @foreach ($posts as $post)
            <livewire:forum.components.post.card
                :$post
                :key="$post->id . $updateKey"
                :selectable="true"
                :show-author-pane="false"
                :show-thread-title="true" />
        @endforeach

        @if ($posts->count() == 0)
            <div class="p-6 border border-slate-300 dark:border-slate-700 rounded-md text-center text-slate-500 text-lg font-medium">
                {{ trans('forum::posts.none_found') }}
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
        toggledAllPosts: false,
        selectedPosts: [],
        showDeleteModal: false,
        showApproveModal: false,

        setButtonsDisabled(disabled) {
            $refs.buttonDelete.disabled = disabled;
            $refs.buttonApprove.disabled = disabled;
        },

        reset() {
            this.toggledAllPosts = false;
            this.selectedPosts = [];
            this.setButtonsDisabled(true);
        },

        onPostChanged(event) {
            if (event.detail.isSelected) {
                this.selectedPosts.push(event.detail.id);
            } else {
                this.selectedPosts.splice(this.selectedPosts.indexOf(event.detail.id), 1);
            }

            this.setButtonsDisabled(this.selectedPosts.length == 0);
        },

        toggleAllPosts(event) {
            this.toggledAllPosts = !this.toggledAllPosts;
            if (!this.toggledAllPosts) this.selectedPosts = [];

            const checkboxes = document.querySelectorAll('[data-post] input[type=checkbox]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.toggledAllPosts;
                checkbox.dispatchEvent(new Event('change'));
            });
        },

        async deleteSelection() {
            const result = await $wire.delete(this.selectedPosts);
            if (result.type == 'success') this.reset();
            this.showDeleteModal = false;
            $dispatch('alert', result);
        },

        async approveSelection() {
            const result = await $wire.approve(this.selectedPosts);
            if (result.type == 'success') this.reset();
            this.showApproveModal = false;
            $dispatch('alert', result);
        }
    }
});
</script>
@endscript
