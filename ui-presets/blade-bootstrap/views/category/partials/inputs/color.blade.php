<div class="mb-3">
    <label for="color">{{ trans('forum::general.color') }}</label>
    <div class="pickr"></div>
    <input type="hidden" value="{{ isset($category->color_light_mode) ? $category->color_light_mode : (old('color') ?? config('forum.frontend.default_category_color')) }}" name="color">
</div>
