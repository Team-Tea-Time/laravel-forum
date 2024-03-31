@extends ('forum::layouts.main', ['category' => null, 'thread' => null, 'breadcrumbs_append' => [trans('forum::general.manage')]])

@section ('content')
    <div class="d-flex flex-row justify-content-between mb-2">
        <h2 class="flex-grow-1">{{ trans('forum::general.manage') }}</h2>

        @can ('createCategories')
            <button type="button" class="btn btn-primary" data-open-modal="create-category">
                {{ trans('forum::categories.create') }}
            </button>

            @include ('forum::category.modals.create')
        @endcan
    </div>

    <div id="manage-categories">
        <draggable-category-list :categories="state.categories"></draggable-category-list>

        <transition name="fade">
            <div v-show="state.changesApplied" class="alert alert-success mt-3" role="alert">
                {{ trans('forum::general.changes_applied') }}
            </div>
        </transition>

        <div class="text-end py-3">
            <button type="button" class="btn btn-primary px-5" :disabled="state.isSavingDisabled" @click="onSave">
                {{ trans('forum::general.save') }}
            </button>
        </div>
    </div>

    <script type="text/x-template" id="draggable-category-list-template">
        <draggable
            :list="categories"
            tag="ul"
            class="list-group"
            @start="drag=true"
            @end="drag=false"
            :group="{ name: 'categories' }"
            item-key="id">
            <template #item="{element}">
                <li class="list-group-item" :data-id="element.id">
                    <a class="float-end btn btn-sm btn-danger ml-2" :href="element.route + '#modal=delete-category'">{{ trans('forum::general.delete') }}</a>
                    <a class="float-end btn btn-sm btn-link ml-2" :href="element.route + '#modal=edit-category'">{{ trans('forum::general.edit') }}</a>
                    <strong :style="{ color: element.color }">@{{ element.title }}</strong>
                    <div class="text-muted">@{{ element.description }}</div>

                    <draggable-category-list :categories="element.children" />
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
                axios.post('{{ route('forum.bulk.category.manage') }}', payload)
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
