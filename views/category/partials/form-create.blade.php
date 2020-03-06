<form class="v-create-category" action="{{ Forum::route('category.store') }}" method="POST">
    {!! csrf_field() !!}

    <button type="button" class="btn btn-primary" @click="isModalOpen = true">
        {{ trans('forum::categories.create') }}
    </button>

    <transition name="slide-fade">
        <div class="modal" tabindex="-1" role="dialog" v-show="isModalOpen" @click="onClickModal">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-sm">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ trans('forum::categories.create') }}</h5>
                        <button type="button" class="close" aria-label="Close" @click="isModalOpen = false">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title">{{ trans('forum::general.title') }}</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="description">{{ trans('forum::general.description') }}</label>
                            <input type="text" name="description" value="{{ old('description') }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="hidden" name="accepts_threads" value="0">
                                <input type="checkbox" name="accepts_threads" value="1" checked>
                                {{ trans('forum::categories.enable_threads') }}
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="hidden" name="is_private" value="0">
                                <input type="checkbox" name="is_private" value="1">
                                {{ trans('forum::categories.make_private') }}
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="color">{{ trans('forum::general.color') }}</label>
                            <div class="pickr"></div>
                            <input type="hidden" value="{{ config('forum.frontend.default_category_color') }}" name="color">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="isModalOpen = false">{{ trans('forum::general.cancel') }}</button>
                        <button type="submit" class="btn btn-primary pull-right">{{ trans('forum::general.create') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</form>

<script>
new Vue({
    el: '.v-create-category',
    data: {
        isModalOpen: false
    },
    methods: {
        onClickModal (event) {
            if (event.target.classList.contains('modal')) {
                this.isModalOpen = false;
            }
        }
    }
});

var input = document.querySelector('input[name=color]');

const pickr = Pickr.create({
    el: '.pickr',
    theme: 'classic',
    default: input.value || null,

    swatches: [
        '{{ config('forum.frontend.default_category_color') }}',
        '#f44336',
        '#e91e63',
        '#9c27b0',
        '#673ab7',
        '#3f51b5',
        '#2196f3',
        '#03a9f4',
        '#00bcd4',
        '#009688',
        '#4caf50',
        '#8bc34a',
        '#cddc39',
        '#ffeb3b',
        '#ffc107'
    ],

    components: {
        preview: true,
        hue: true,
        interaction: {
            input: true,
            save: true
        }
    },

    strings: {
        save: 'Apply'
    }
});

pickr
    .on('save', instance => pickr.hide())
    .on('clear', instance => {
        input.value = '';
        input.dispatchEvent(new Event('change'));
    })
    .on('cancel', instance => {
        const selectedColor = instance
            .getSelectedColor()
            .toHEXA()
            .toString();

        input.value = selectedColor;
        input.dispatchEvent(new Event('change'));
    })
    .on('change', (color, instance) => {
        const selectedColor = color
            .toHEXA()
            .toString();

        input.value = selectedColor;
        input.dispatchEvent(new Event('change'));
    });
</script>