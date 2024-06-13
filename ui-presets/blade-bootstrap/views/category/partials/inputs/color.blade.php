<div class="mb-3">
    <label for="color_light_mode">{{ trans('forum::general.color') }}</label>
    <div class="pickr"></div>
    <input type="hidden"
        value="{{ isset($category->color_light_mode) ? $category->color_light_mode : (old('color_light_mode') ?? config('forum.frontend.default_category_color')) }}"
        name="color_light_mode" />
</div>
