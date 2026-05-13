@extends ('forum::layouts.main', ['category' => null, 'thread' => null, 'breadcrumbs_append' => [trans('forum::general.manage')]])

@section ('content')
    <section class="forum-hero">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="forum-hero-kicker">{{ trans('forum::general.manage') }}</div>
                <h1 class="forum-hero-title">{{ trans('forum::categories.manage') }}</h1>
                <p class="forum-hero-copy">Reorder categories and tune the forum structure without leaving the page.</p>
            </div>

            @can ('createCategories')
                <x-forum::button type="button" data-open-modal="create-category">
                    <i data-feather="plus" class="h-4 w-4"></i>
                    {{ trans('forum::categories.create') }}
                </x-forum::button>

                @include ('forum::category.modals.create')
            @endcan
        </div>
    </section>

    @if ($categories->isEmpty())
        <div class="forum-empty">
            {{ trans('forum::categories.none') }}
        </div>
    @else
        <div id="manage-categories" class="space-y-4">
            <draggable-category-list :categories="state.categories"></draggable-category-list>

            <transition name="fade">
                <div v-show="state.changesApplied" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-100" role="alert">
                    {{ trans('forum::general.changes_applied') }}
                </div>
            </transition>

            <div class="flex justify-end">
                <button type="button" class="forum-button disabled:opacity-50" :disabled="state.isSavingDisabled" @click="onSave">
                    {{ trans('forum::general.save') }}
                </button>
            </div>
        </div>
    @endif

    <script type="text/x-template" id="draggable-category-list-template">
        <draggable
            :list="categories"
            tag="ul"
            class="space-y-3"
            @start="drag=true"
            @end="drag=false"
            :group="{ name: 'categories' }"
            :empty-insert-threshold="50"
            item-key="id">
            <template #item="{element}">
                <li class="forum-panel p-4" :data-id="element.id">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-3">
                            <span class="mt-1 grid h-8 w-8 shrink-0 cursor-move place-items-center rounded-full bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-300">
                                ::
                            </span>
                            <span>
                                <strong class="text-lg" :style="{ color: element.color }">@{{ element.title }}</strong>
                                <div class="forum-muted">@{{ element.description }}</div>
                            </span>
                        </div>
                        <span class="flex flex-wrap gap-2">
                            <a class="forum-button-secondary text-sm" :href="element.route + '#modal=edit-category'">{{ trans('forum::general.edit') }}</a>
                            <a class="forum-button forum-button-danger text-sm" :href="element.route + '#modal=delete-category'">{{ trans('forum::general.delete') }}</a>
                        </span>
                    </div>

                    <span v-if="element.children.length == 0" class="mt-3 block min-h-10 rounded-lg border border-dashed border-slate-300 dark:border-white/15">
                        <draggable-category-list :categories="element.children" />
                    </span>
                    <div v-else class="mt-3 pl-4 sm:pl-8">
                        <draggable-category-list :categories="element.children" />
                    </div>
                </li>
            </template>
        </draggable>
    </script>

    <script type="module">
    const app = Vue.createApp({
        setup() {
            const state = Vue.reactive({
                categories: @json($categories),
                isSavingDisabled: true,
                changesApplied: false,
            });

            Vue.watch(
                () => state.categories,
                async (newValue, oldValue) => {
                    state.isSavingDisabled = false;
                },
                { deep: true }
            );

            function onSave()
            {
                state.isSavingDisabled = true;
                state.changesApplied = false;

                var payload = { categories: state.categories };
                axios.post('{{ route('forum.bulk.category.reorder') }}', payload)
                    .then(response => {
                        state.changesApplied = true;
                        setTimeout(() => state.changesApplied = false, 3000);
                    })
                    .catch(error => {
                        state.isSavingDisabled = false;
                        console.log(error);
                    });
            }

            return {
                state,
                onSave
            };
        }
    });

    app.component(
        'Draggable',
        VueDraggable
    );

    app.component(
        'DraggableCategoryList',
        {
            props: ['categories'],
            template: '#draggable-category-list-template',
        }
    );

    app.mount('#manage-categories');
    </script>
@stop
