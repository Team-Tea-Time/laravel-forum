<form action="{{ Forum::route('category.store') }}" method="POST">
    {!! csrf_field() !!}

    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createCategoryModal">
    {{ trans('forum::categories.create') }}
    </button>

    <div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createCategoryModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{ trans('forum::categories.create') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('forum::general.cancel') }}</button>
                    <button type="submit" class="btn btn-primary pull-right">{{ trans('forum::general.create') }}</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    var $input = $('input[name=color]');

    const pickr = Pickr.create({
        el: '.pickr',
        theme: 'classic',
        default: $input.val() || null,

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
        }
    });

    pickr
        .on('clear', instance => {
            $input.val('').trigger('change');
        })
        .on("cancel", instance => {
            const selectedColor = instance
                .getSelectedColor()
                .toHEXA()
                .toString();

            $input.val(selectedColor).trigger('change');
        })
        .on('change', (color, instance) => {
            const selectedColor = color
                .toHEXA()
                .toString();

            $input.val(selectedColor).trigger('change');
        });
});
</script>