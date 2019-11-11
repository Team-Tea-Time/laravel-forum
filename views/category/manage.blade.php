@extends ('forum::master', ['category' => null, 'thread' => null, 'breadcrumb_other' => trans('forum::general.manage') ])

@section ('content')
    <div class="d-flex flex-row justify-content-between mb-2">
        <h2 class="flex-grow-1">{{ trans('forum::general.manage') }}</h2>

        @can ('createCategories')
            @include ('forum::category.partials.form-create')
        @endcan
    </div>

    <div class="v-manage-categories">
        <draggable-category-list :categories="categories"></draggable-category-list>

        <transition name="fade">
            <div v-show="changesApplied" class="alert alert-success mt-3" role="alert">
                {{ trans('forum::general.changes_applied') }}
            </div>
        </transition>

        <div class="text-right py-3">
            <button type="button" class="btn btn-primary btn-lg" :disabled="isSavingDisabled" @click="onSave">
                {{ trans('forum::general.save') }}
            </button>
        </div>
    </div>

    <script type="text/x-template" id="draggable-category-list-template">
        <draggable tag="ul" class="list-group" :list="categories" group="categories" :invertSwap="true">
            <li class="list-group-item" v-for="category in categories" :data-id="category.id" :key="category.id">
                <strong :style="{ color: category.color }">@{{ category.title }}</strong>
                <button class="float-right btn btn-sm ml-2">{{ trans('forum::general.edit') }}</button>
                <span class="float-right text-muted">@{{ category.description }}</span>

                <draggable-category-list :categories="category.children"></draggable-category-list>
            </li>
        </draggable>
    </script>

    <script>
    var draggableCategoryList = {
        name: 'draggable-category-list',
        template: '#draggable-category-list-template',
        props: ['categories']
    };

    new Vue({
        el: '.v-manage-categories',
        components: {
            draggableCategoryList
        },
        data: {
            categories: @json($categories),
            isSavingDisabled: true,
            changesApplied: false
        },
        watch: {
            categories: {
                handler: function (categories) {
                    this.isSavingDisabled = false;
                },
                deep: true
            }
        },
        methods: {
            onSave: function () {
                this.isSavingDisabled = true;
                this.changesApplied = false;

                var payload = { categories: this.categories };
                var options = {
                    headers: { Authorization: 'Bearer {{ auth()->user()->api_token }}' }
                };

                axios.post('{{ route('forum.api.bulk.category.manage') }}', payload, options)
                    .then(response => {
                        this.changesApplied = true;
                        setTimeout(() => this.changesApplied = false, 3000);
                    })
                    .catch(error => {
                        this.isSavingDisabled = false;
                        console.log(error);
                    });
            }
        }
    });
    </script>
@stop