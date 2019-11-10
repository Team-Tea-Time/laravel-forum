@extends ('forum::master', ['category' => null, 'thread' => null])

@section ('content')
    <div class="d-flex flex-row justify-content-between mb-2">
        <h2 class="flex-grow-1">{{ trans('forum::general.manage') }}</h2>

        @can ('createCategories')
            @include ('forum::category.partials.form-create')
        @endcan
    </div>

    <div class="v-manage-categories">
        <draggable-category-list :categories="categories"></draggable-category-list>

        <hr>

        <transition name="fade">
            <div v-show="changesApplied" class="alert alert-success" role="alert">
                {{ trans('forum::general.changes_applied') }}
            </div>
        </transition>

        <button type="button" class="btn btn-primary" :disabled="isSavingDisabled" @click="onSave">
            {{ trans('forum::general.save') }}
        </button>
    </div>

    <script type="text/x-template" id="draggable-category-list-template">
        <draggable tag="ul" class="list-group" :list="categories" group="categories" :invertSwap="true">
            <li class="list-group-item" v-for="category in categories" :data-id="category.id" :key="category.id">
                <strong :style="{ color: category.color }">@{{ category.title }}</strong>
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
            categories: function (categories) {
                this.isSavingDisabled = false;
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