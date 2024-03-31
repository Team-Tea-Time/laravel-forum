<div x-data="manage">
    @include ('forum::components.loading-overlay')
    @include ('forum::components.breadcrumbs')

    <div class="flex justify-center items-center">
        <div class="grow max-w-screen-lg">
            <h1>{{ trans('forum::general.manage') }}</h1>

            @can ('createCategories')
                <div class="mb-6 text-right">
                    <x-forum::link-button
                        :label="trans('forum::categories.create')"
                        icon="squares-plus-outline"
                        :href="Forum::route('category.create')" />
                </div>
            @endcan

            <div class="bg-white dark:bg-slate-700 rounded-md shadow-md my-2 p-6">
                <ol id="category-tree">
                    @include ('forum::components.category.draggable-items', ['categories' => $categories])
                </ol>

                <div class="mt-4 text-right">
                    <x-forum::button
                        id="save"
                        :label="trans('forum::general.save')"
                        x-ref="button"
                        @click="save"
                        disabled />
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
Alpine.data('manage', () => {
    return {
        nestedSort: null,
        init () {
            this.initialiseNestedSort();
        },
        initialiseNestedSort() {
            this.nestedSort = new NestedSort({
                propertyMap: {
                    id: 'id',
                    parent: 'parent_id',
                    text: 'title',
                },
                actions: {
                    onDrop: () => $refs.button.disabled = false
                },
                el: '#category-tree',
                listItemClassNames: 'border border-slate-300 rounded-md text-lg p-4 my-2'
            });
        },
        getItems (ol) {
            let tree = [];
            for (let i = 0; i < ol.children.length; ++i) {
                let item = { id: ol.children[i].dataset.id, children: [] };
                for (let j = 0; j < ol.children[i].children.length; ++j) {
                    if (ol.children[i].children[j].tagName == 'OL') {
                        item.children = this.getItems(ol.children[i].children[j]);
                    }
                }

                tree.push(item);
            }

            return tree;
        },
        async save () {
            let tree = this.getItems(document.getElementById('category-tree'));
            $wire.tree = tree;
            const result = await $wire.save();
            $dispatch('alert', result);
            this.initialiseNestedSort();
        }
    }
});
</script>
@endscript
