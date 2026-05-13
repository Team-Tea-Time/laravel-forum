<div>
    <label for="color_light_mode" class="forum-label">{{ trans('forum::general.color') }}</label>
    <div class="pickr mt-2"></div>
    <input type="hidden"
        value="{{ isset($category->color_light_mode) ? $category->color_light_mode : (old('color_light_mode') ?? config('forum.frontend.default_category_color')) }}"
        name="color_light_mode" />
</div>
