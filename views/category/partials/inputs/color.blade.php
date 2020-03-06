<div class="form-group">
    <label for="color">{{ trans('forum::general.color') }}</label>
    <div class="pickr"></div>
    <input type="hidden" value="{{ isset($category->color) ? $category->color : (old('color') ?? config('forum.frontend.default_category_color')) }}" name="color">
</div>

<script>
document.addEventListener('DOMContentLoaded', event =>
{ 
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
        .on('clear', instance =>
        {
            input.value = '';
            input.dispatchEvent(new Event('change'));
        })
        .on('cancel', instance =>
        {
            const selectedColor = instance
                .getSelectedColor()
                .toHEXA()
                .toString();

            input.value = selectedColor;
            input.dispatchEvent(new Event('change'));
        })
        .on('change', (color, instance) =>
        {
            const selectedColor = color
                .toHEXA()
                .toString();

            input.value = selectedColor;
            input.dispatchEvent(new Event('change'));
        });
});
</script>