@extends ('forum::layouts.main', ['category' => null, 'thread' => null, 'breadcrumbs_append' => [trans('forum::general.manage')]])

@section ('content')
    <div class="flex flex-row justify-between mb-2">
        <h2 class="text-3xl font-medium my-3">{{ trans('forum::general.manage') }}</h2>

        @can ('createCategories')
            <x-forum::button type="button" data-open-modal="create-category" class="px-6">
                {{ trans('forum::categories.create') }}
            </x-forum::button>

            @include ('forum::category.modals.create')
        @endcan
    </div>

    <div id="manage-categories">
        <draggable-category-list :categories="state.categories"></draggable-category-list>

        <transition name="fade">
            <div v-show="state.changesApplied" class="bg-green-100 mb-4 text-green-700 mt-3 px-4 py-3" role="alert">
                {{ trans('forum::general.changes_applied') }}
            </div>
        </transition>

        <div class="flex justify-end py-3">
            <button type="button" class="bg-blue-500 text-white rounded py-2 px-8 hover:cursor-pointer disabled:opacity-50" :disabled="state.isSavingDisabled" @click="onSave">
                {{ trans('forum::general.save') }}
            </button>
        </div>
    </div>

    <script type="text/x-template" id="draggable-category-list-template">
        <draggable
            :list="categories"
            tag="ul"
            @start="drag=true"
            @end="drag=false"
            :group="{ name: 'categories' }"
            item-key="id">
            <template #item="{element}">
                <li class="bg-white p-4 my-2 rounded-md border" :data-id="element.id">
                    <span class="flex">
                        <span class="grow">
                            <strong :style="{ color: element.color }">@{{ element.title }}</strong>
                            <div class="text-muted">@{{ element.description }}</div>
                        </span>
                        <span>
                            <a class="text-blue-500 hover:text-blue-800 px-3 py-2 text-sm ml-2" :href="element.route + '#modal=edit-category'">{{ trans('forum::general.edit') }}</a>
                            <a class="bg-red-500 hover:bg-red-400 text-white hover:text-white px-3 py-2 text-sm rounded ml-2" :href="element.route + '#modal=delete-category'">{{ trans('forum::general.delete') }}</a>
                        </span>
                    </span>

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
